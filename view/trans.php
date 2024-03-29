<!--****************************************************************************************-->

<?php

session_start();
?>
<!--****************************************************************************************-->

<!DOCTYPE html>
<html>
   <head>
      <meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <title>Transformation Graphical editor</title>
      <meta name="description" content="Trace transformation">
      <meta charset="UTF-8">

      <script src="../lib/Chart_files/analytics.js" async=""></script>
      <script src="../lib/Chart_files/go_002.js"></script><script src="../lib/Chart_files/go.js"></script>

      <link href="../lib/Chart_files/goSamples.css" rel="stylesheet" type="text/css">  <!-- you don't need to use this -->

      <script src="../lib/Chart_files/goSamples.js"></script>
      <script src="../lib/Chart_files/highlight.js"></script>

      <link href="../lib/Chart_files/highlight.css" rel="stylesheet" type="text/css">  <!-- this is only for the GoJS Samples framework -->


<!--****************************************************************************************-->

<script type="text/javascript">
    
    function switchInfoPerso(x)
    {
  
      divInfo = document.getElementById(x);
      if (divInfo.style.display == 'none')
          divInfo.style.display ='block';else divInfo.style.display = 'none';
    }
    </script>

<!--****************************************************************************************-->

      <script id="code">

//*****************************************************************************************
  function init() {
    if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
    var $ = go.GraphObject.make;  // for conciseness in defining templates

    myDiagram =
      $(go.Diagram, "myDiagramDiv",  // must name or refer to the DIV HTML element
        {
          // start everything in the middle of the viewport
          initialContentAlignment: go.Spot.Center,
          // have mouse wheel events zoom in and out instead of scroll up and down
          "toolManager.mouseWheelBehavior": go.ToolManager.WheelZoom,
          // support double-click in background creating a new node
          "clickCreatingTool.archetypeNodeData": { text: "trace" },
          // enable undo & redo
          "undoManager.isEnabled": true
        });

//*****************************************************************************************      
    // when the document is modified, add a "*" to the title and enable the "Save" button
  
  
    myDiagram.addDiagramListener("Modified", function(e) {
      var button = document.getElementById("SaveButton");
      if (button) button.disabled = !myDiagram.isModified;
      var idx = document.title.indexOf("*");
      if (myDiagram.isModified) {
        if (idx < 0) document.title += "*";
      } else {
        if (idx >= 0) document.title = document.title.substr(0, idx);
      }
    });

    // define the Node template
    myDiagram.nodeTemplate =
      $(go.Node, "Auto",
        new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
        // define the node's outer shape, which will surround the TextBlock
        $(go.Shape, "RoundedRectangle",
          {
            parameter1: 20,  // the corner has a large radius
            fill: $(go.Brush, "Linear", { 0: "rgb(235, 222, 240 )", 1: "rgb(235, 222, 240 )" }),
            stroke: null,
            portId: "",  // this Shape is the Node's port, not the whole Node
            fromLinkable: true, fromLinkableSelfNode: true, fromLinkableDuplicates: true,
            toLinkable: true, toLinkableSelfNode: true, toLinkableDuplicates: true,
            cursor: "pointer"
          }),
        $(go.TextBlock,
          {
            font: "11pt helvetica, arial, sans-serif",
            editable: true  // editing the text automatically updates the model data
          },
          new go.Binding("text").makeTwoWay())
      );

    // unlike the normal selection Adornment, this one includes a Button
    myDiagram.nodeTemplate.selectionAdornmentTemplate =
      $(go.Adornment, "Spot",
        $(go.Panel, "Auto",
          $(go.Shape, { fill: null, stroke: "white", strokeWidth: 1 }),
          $(go.Placeholder)  // a Placeholder sizes itself to the selected Node
        ),
        // the button to create a "next" node, at the top-right corner
        $("Button",
          {
            alignment: go.Spot.TopRight,
            click: addNodeAndLink  // this function is defined below
          },
          $(go.Shape, "PlusLine", { width: 6, height: 6 })
        ) // end button
      ); // end Adornment

//*****************************************************************************************
    
    // clicking the button inserts a new node to the right of the selected node,
    // and adds a link to that new node
//*****************************************************************************************    
    function addNodeAndLink(e, obj) {
      var adornment = obj.part;
      var diagram = e.diagram;
      diagram.startTransaction("Add State");

      // get the node data for which the user clicked the button
      var fromNode = adornment.adornedPart;
      var fromData = fromNode.data;
      // create a new "State" data object, positioned off to the right of the adorned Node
      var toData = { text: "trace" };
      var p = fromNode.location.copy();
      p.x += 200;
      toData.loc = go.Point.stringify(p);  // the "loc" property is a string, not a Point object
      // add the new node data to the model
      var model = diagram.model;
      model.addNodeData(toData);

      // create a link data from the old node data to the new node data
      var linkdata = {
        from: model.getKeyForNodeData(fromData),  // or just: fromData.id
        to: model.getKeyForNodeData(toData),
        text: "filter"
      };
      // and add the link data to the model
      model.addLinkData(linkdata);

      // select the new Node
      var newnode = diagram.findNodeForData(toData);
      diagram.select(newnode);

      diagram.commitTransaction("Add State");

      // if the new node is off-screen, scroll the diagram to show the new node
      diagram.scrollToRect(newnode.actualBounds);
    }

    // replace the default Link template in the linkTemplateMap
    myDiagram.linkTemplate =
      $(go.Link,  // the whole link panel
        {
          curve: go.Link.Bezier, adjusting: go.Link.Stretch,
          reshapable: true, relinkableFrom: true, relinkableTo: true,
          toShortLength: 3
        },
        new go.Binding("points").makeTwoWay(),
        new go.Binding("curviness"),
        $(go.Shape,  // the link shape
          { strokeWidth: 1.5 }),
        $(go.Shape,  // the arrowhead
          { toArrow: "standard", stroke: null }),
        $(go.Panel, "Auto",
          $(go.Shape,  // the label background, which becomes transparent around the edges
            {
              fill: $(go.Brush, "Radial",
                      { 0: "rgb(240, 240, 240)", 0.3: "rgb(240, 240, 240)", 1: "rgba(240, 240, 240, 0)" }),
              stroke: null
            }),
          $(go.TextBlock, "trace",  // the label text
            {
              textAlign: "center",
              font: "9pt helvetica, arial, sans-serif",
              margin: 4,
              editable: true  // enable in-place editing
            },
            // editing the text automatically updates the model data
            new go.Binding("text").makeTwoWay())
        )
      );

    // read in the JSON data from the "mySavedModel" element
    load();
  }

//*****************************************************************************************

  // Show the diagram's model in JSON format
  function save() {

    document.getElementById("mySavedModel").value = myDiagram.model.toJson();

  }

//*****************************************************************************************

  function load() {
    myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);
  }
</script>



<!--****************************************************************************************-->
<!--****************************************************************************************-->
<!--****************************************************************************************-->


<style></style></head>
<body onload="init()">


<form name="transfo" action="../lib/request.php?Action=simpletransformation" method="post">



<!--****************************************************************************************-->
<!--Execute related transformation and obrain values-->

<!--****************************************************************************************-->
 



  <input type="submit" name="ExecuteTransformation"  value= "Compute indicator"  onclick="save()"/>


<!--****************************************************************************************-->


<!--****************************************************************************************-->
<!--Put the transformation editor-->
<!--****************************************************************************************-->


<div id="sample">
  <div id="myDiagramDiv" style="border: 1px solid black; width: 100%; height: 400px; position: relative; cursor: auto;"><canvas height="400" width="1136" style="position: absolute; top: 100px; left: 0px; z-index: 2; -moz-user-select: none; width: 1136px; height: 400px; cursor: auto;" tabindex="0">This text is displayed if your browser does not support the transormation editor.</canvas><div style="position: absolute; overflow: auto; width: 1136px; height: 400px; z-index: 1;"><div style="position: absolute; width: 1px; height: 1px;">
    
  </div>
  </div>
</div>

<br>
<br>

<!--****************************************************************************************-->
<!--Put the values etidor and visualisation forms-->
<!--****************************************************************************************-->

  <div>
   

    <textarea name="DiagramText" id="mySavedModel" style= "display:none; width:0eight:0px"><?php if (isset($_SESSION['diagram'])) echo ltrim($_SESSION['diagram']) ;?></textarea>


  </div>

  <!--****************************************************************************************-->

   <div  style="float:left; /* Le cadre sort du flux */
   border:1px solid #D0FA58;
   height:245px;
   width:300px;"> 

   <textarea name="resultat"  cols="35" rows="16" style="background:#D0FA58"><?php if (isset($_SESSION['result'])) echo ltrim($_SESSION['result']);?></textarea>
           
   </div>
<!--****************************************************************************************-->

   <div    style="float:left; /* ou margin-left:202px;  */
   border:1px solid gray; 
   height:245px;
   width:700px;">
  <?php
   require_once("indicatorkinds.php");

   ?>

   </div>
<!--****************************************************************************************-->

<!--****************************************************************************************-->
<!-- Save indicator-->
<!--****************************************************************************************-->

<br>
<br>
<br>
<br>
</form>
<table>
  <tr>
    <td><br>

<input type="button" class="bouton" value="Save Indicator " onclick="switchInfoPerso('indicatorinfo');" />

      
    </td>
  </tr>
</table>

<form name="transfo" action="../lib/request.php?Action=SaveIndicators" method="post">

   <div id="indicatorinfo" style="border-style: solid; display: none; border-color: #F4D03F;">
    <br><table border="0" cellpadding="1" cellspacing="1">
      <tbody>
        <tr>
           
          <td>Indicator Name: </td>
          <td><input name="IndicatorName" type="text" value=<?php if (isset($_SESSION['CurrentIndicatorName'])) echo $_SESSION['CurrentIndicatorName']; ?> /></td>
        </tr>

        <tr>
          <td>subject: </td>
          <td><textarea cols="40" name="IndicatorSubject" rows="10"></textarea></td>

        </tr>

        <tr><td><p>&nbsp;&nbsp;<input type = "submit" name="SaveIndicator" value="Save indicator"/>&nbsp;&nbsp;<td/>
        </tr>
  
    </table>
    <br>

   </div>

</body></html>
