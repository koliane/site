<script>
/*bar = { x: .., open: .., high: .., low: .., close: .., id: .. }*/
function GrBar( bar ) {
    this.x = bar.x;
    this.yOpen = bar.open;
    this.yHigh = bar.high;
    this.yLow = bar.low;
    this.yClose = bar.close;
    this.id = bar.id;
}
GrBar.prototype = {
    constructor: GrBar,
    paint: function(){

    }
}
</script>
