<script>
function Pair( name, timeFrame, managerSettings ){
    self = this;
    this.name=name;
    this.timeFrames=[];


    this.timeFrames.push( new TimeFrame(self.name, timeFrame, managerSettings));
}
Pair.prototype = {
    constructor: Pair,
}
</script>