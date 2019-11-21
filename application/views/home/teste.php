<span id="annotations"></span>
<script type="text/javascript">
	
function roda(){
	return functionPlot({
  target: '#annotations',
  yAxis: {domain: [0, 16]},
  xAxis: {domain: [0, 16]},
  data: [

  {
    fn: '-1.5x+15',

  },
  {
    points: [
      [1, 1],
      [2, 1],
      [2, 2],
      [1, 2],
      [1, 1]
    ],
    color: 'black',
    fnType: 'points',
    graphType: 'scatter'
  } 
  ],
  annotations: [{
    x: -1
  }, {
    x: 1,
    text: 'x = 1'
  },
  {
    x: 0,
    y: 2,
    text: 'y >= 0'
  },
  {
    y: 0,
    text: 'x >= 0'
  },
   {
    y: 2,
    text: 'y = 2'
  }]
})
	}
</script>

<script type="text/javascript"> roda();</script>