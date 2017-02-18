<script>
function Pair( name, timeFrame ){
    this.name=name;
    this.timeFrames=[];

    this.timeFrames.push( new TimeFrame());
}
Pair.prototype = {
    constructor: Pair,
}
</script>