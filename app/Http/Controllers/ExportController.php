<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ExportController extends Controller
{
    public function generateSpectralGraph($historyId, $stepIndex, $graphIndex)
    {
        // Retrieve the history record by ID
        $historyRecord = \App\Models\History::find($historyId);
        if ($historyRecord) {
            $graphData = $historyRecord->steps[$stepIndex]['graphs'][$graphIndex];
        }

        // Prepare data points for the contour plot
        foreach ($graphData['lines'] as $lineKey => $lineData) {
            $index = 0;
            $pointGroup = [];
            $formattedData = [];

            foreach ($lineData['points'] as $pointIndex => $point) {
                // Collect Y-values for each point
                $pointGroup[] = $point['y'];

                // Group data points every 3 points
                if (($pointIndex + 1) % 3 == 0 && $pointIndex > 0) {
                    $formattedData[$index] = $pointGroup;
                    $pointGroup = [];
                    $index++;
                }
            }

            // Initialize a graph object with defined dimensions
            $graph = new \Graph\Graph(800, 600);
            $graph->SetScale('intint');
            $graph->SetMargin(30, 120, 40, 30);

            // Set graph title and hide x-axis labels
            $graph->title->Set($graphData['name']);
            $graph->xaxis->HideLabels(true);

            // Create and configure a contour plot
            $contourPlot = new \Plot\FilledContourPlot($formattedData, 150);
            $contourPlot->SetInvert();
            $contourPlot->SetFilled(true);
            $contourPlot->ShowLines(false);
            $contourPlot->ShowLabels(false);

            // Add the contour plot to the graph
            $graph->Add($contourPlot);
        }

        // Render the graph
        $graph->Stroke();
    }
    
    public function convertGraphToImage($historyId, $stepIndex, $graphIndex)
    {
        // Retrieve the specified history record
        $historyRecord = \App\Models\History::find($historyId);
        if ($historyRecord) {
            $graphDetails = $historyRecord->steps[$stepIndex]['graphs'][$graphIndex];
        }

        // Initialize a new pie graph with custom dimensions
        $pieGraph = new \Graph\PieGraph(350, 250);
        $pieGraph->title->Set("A Simple Pie Plot");
        $pieGraph->SetBox(true);

        // Sample data for pie chart representation
        $chartData = [40, 21, 17, 14, 23];
        $piePlot = new \Plot\PiePlot($chartData);

        // Customize pie plot appearance
        $piePlot->ShowBorder();                    // Display border around each slice
        $piePlot->SetColor('black');               // Set border color
        $piePlot->SetSliceColors(['#1E90FF', '#2E8B57', '#ADFF2F', '#DC143C', '#BA55D3']); // Custom colors for slices

        // Add the pie plot to the graph
        $pieGraph->Add($piePlot);

        // Render the graph as an image
        $pieGraph->Stroke();
    }

    public function exportPDF(Request $request)
    {
        $id = $request->input('id');
        $graph = $request->input('graph');
        if ($graph) {
            $graph = json_decode($graph, true);
        } else {
            $graph = [];
        }
        //$item = \App\Models\History::with('display.workstation.workgroup')->find($id);
        $item = \App\Models\History::find($id);
        $item->load('display.workstation.workgroup');
        
        $version = File::get(base_path().'/version.txt');

        $pdf = \PDF::loadView('histories.pdf',  compact('item', 'graph', 'version'));
        return $pdf->download($item->name . '.pdf');
    }
}
