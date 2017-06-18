<script>
function Pair( name, timeFrame, id, managerSettings ){
    self = this;
    this.name=name;
    this.timeFrames=[];
    this.decimalPlaces = managerSettings.decimalPlaces;

    this.id = id;

    this.timeFrames.push( new TimeFrame(self.name, timeFrame, managerSettings));
}
Pair.prototype = {
    constructor: Pair,
	addTimeframe: function(name, timeFrame, managerSettings){
		// if()
			alert();
		// this.timeFrames.push( new TimeFrame(name, timeFrame, managerSettings));
	},
}
</script>