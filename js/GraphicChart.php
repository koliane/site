<script>
    /* grSettings = { barWidth: .., barSpacing: .., priceIndicatorWidth: .., timeIndicatorHeight: .., topIndent: .., bottomIndent: .. }*/

    function GrChart(width, height, grSettings) {
        var self = this;

        /**Размеры canvas*/
        this.width = width;
        this.height = height;

        /**Отступы (аналогично padding, только для графика в canvas)*/
        this.topIndent = grSettings.topIndent;
        this.bottomIndent = grSettings.bottomIndent || grSettings.topIndent;
        this.leftIndent = grSettings.leftIndent;
        this.rightIndent = grSettings.rightIndent;

//        this.priceIndicatorWidth = grSettings.priceIndicatorWidth;
//        this.timeIndicatorHeight = grSettings.timeIndicatorHeight;

        /**Размеры canvas графика(котировок)*/
//        this.workWidth = width - grSettings.priceIndicatorWidth;
//        this.workHeight = height - grSettings.timeIndicatorHeight;

        /**Размеры графика в canvas( учитывая padding)*/
        this.chartWidth = this.width - this.leftIndent - this.rightIndent;
        this.chartHeight = this.height - this.topIndent - this.bottomIndent;

        this.id = ++grSettings.lastChartIndex;

        this.widthBar = grSettings.widthBar;
        this.barSpacing = grSettings.barSpacing;

        /**Максимальное число баров, которые могут уместиться на графике*/
        this.maxCountBarOnChart;
        /**Количество баров на графике в данный момент*/
        this.currentCountBarOnChart;

        /**Максимальная и минимальные цены на графике*/
        this.maxPriceOnChart;
        this.minPriceOnChart;

        /**Отступ от левого края для первого бара (может не входить полностью в canvas)*/
        this.firstBarIndent;

        this.canvas = document.getElementById("main_canvas"+this.id);
//        console.log(this.width,this.height, this.topIndent, this.bottomIndent
//                    ,this.workWidth,this.workHeight, this.id, this.widthBar, this.barSpacing, this.rightIndent );
//        console.log(grSettings.topIndent);

    }
    GrChart.prototype = {
        constructor: GrChart,
        paint: function( arrBars, settings, prevSettings){

        },
        convertToPaint: function( arrBars ){

        }
    }

</script>