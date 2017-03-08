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
}
</script>