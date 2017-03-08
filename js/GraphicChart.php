<script>
    /* grSettings = { barWidth: .., barSpacing: .., priceIndicatorWidth: .., timeIndicatorHeight: .., topIndent: .., bottomIndent: .. }*/

    function GrChart(width, height, grSettings, context) {
        var self = this;

        this.namePair = grSettings

        this.arrBars = context.arrPairs[0].timeFrames[0].fullChart;
//        this.arrGrBars = [];
        /**Размеры canvas*/
        this.width = width;
        this.height = height;


        /**Отступы (аналогично padding, только для графика в canvas)*/
        this.topIndent = grSettings.topIndent;
        this.bottomIndent = grSettings.bottomIndent || grSettings.topIndent;
//        this.leftIndent = grSettings.leftIndent;
        this.rightIndent = grSettings.rightIndent;

//        this.priceIndicatorWidth = grSettings.priceIndicatorWidth;
//        this.timeIndicatorHeight = grSettings.timeIndicatorHeight;

        /**Размеры canvas графика(котировок)*/
//        this.workWidth = width - grSettings.priceIndicatorWidth;
//        this.workHeight = height - grSettings.timeIndicatorHeight;

        /**Размеры графика в canvas( учитывая padding)*/
        this.chartWidth = this.width;
//        this.chartWidth = this.width  - this.rightIndent;
        this.chartHeight = this.height - this.topIndent - this.bottomIndent;

        this.id = grSettings.lastChartIndex;

        this.widthBar = grSettings.widthBar;/**Необходимо указывать четным, либо 1*/
        this.barSpacing = grSettings.barSpacing;

        /**Максимальное число баров, которые могут уместиться на графике*/
        this.maxCountBarOnChart;
        /**Количество баров на графике в данный момент*/
        this.currentCountBarOnChart;

        /**Максимальная и минимальные цены на графике*/
        this.maxPriceOnChart = 0;
        this.minPriceOnChart = 9999999999;

        /**Отступ от левого края для первого бара (может не входить полностью в canvas)*/
        this.firstBarIndent;

        /**Цвета баров*/
        this.buyBarColor = grSettings.buyBarColor;
        this.sellBarColor = grSettings.sellBarColor;

        this.bodyBorderBarColor =  grSettings.bodyBorderBarColor;
        this.topShadowBarColor =  grSettings.topShadowBarColor;
        this.bottomShadowBarColor =  grSettings.bottomShadowBarColor;

        /**Количество цифр после запятой, которые учитываются в вычислениях*/
        this.decimalPlaces = grSettings.decimalPlaces;
        this.defaultPriceStep = Math.pow(10, -this.decimalPlaces);

        this.yPxStep;
        this.yPriceStep;


        this.canvas = document.getElementById("canvas"+this.id);
        this.gr = this.canvas.getContext("2d");

//        this.convertBarsToGraphic(this.arrBars);
        console.log( this.convertBarsToGraphic(this.arrBars) );
        console.table( this.convertBarsToGraphic(this.arrBars) );
        this.paintArrBars(this.convertBarsToGraphic(this.arrBars));
//        this.paint();

        console.log(this.chartHeight)

//        console.log('this.yPxStep');
//        console.log(this.yPxStep);
//        console.log('this.yPriceStep');
//        console.log(this.yPriceStep);

//        console.log('y');
//        console.log((this.maxPriceOnChart - this.arrBars[0].high)/ this.yPriceStep);
//        y =  Math.round( (this.maxPriceOnChart - this.arrBars[0].high) / this.yPriceStep );
//        console.log(y);


    }
    GrChart.prototype = {
        constructor: GrChart,
        paint: function( arrGrBars, leftBarIndent ){
                    this.paintBar( 100, 50, 100, 110, 200, true);
        //            leftBarIndent = leftBarIndent || 0;
        //            var self = this;
        //            arrGrBars.forEach(function(item, i, arr){
        //                /**Если очередной бар - бар продажи, то рисуем бар продажи, иначе рисуем бар покупки*/
        //                if( item['open'] < item['close'])
        //                    self.paintBar(  self.widthBar + leftBarIndent, self._convertY(item['high']), self._convertY(item['open']),
        //                                    self._convertY(item['close']), self._convertY(item['low']), false  );
        //                else
        //                    self.paintBar(  self.widthBar + leftBarIndent, self._convertY(item['high']), self._convertY(item['close']),
        //                                    self._convertY(item['open']), self._convertY(item['low']), true  );
        //            });


                },
        /**
         * Функция отрисовки бара. Входные параметры заданы в пикселях.
         * @param x - горизонтальная координата, где будет находится середина бара
         * @param yH - вертикальная координата, где будет начинаться high бара
         * @param yBody
         * @param yCloseBody
         * @param yL
         * @direction - тип бара(от этого параметра зависит цвет заливки тела бара). Если true - бар покупки.
         */
        paintBar: function( x, yH , yBody, yCloseBody, yL, direction){
            var gr = this.gr;
            var bodyColor = direction ? this.buyBarColor : this.sellBarColor;


            /* xRatio - смещение бара на определенное значение в px (для более четкого отображения) по x
            * yRatio - смещение бара на определенное значение в px (для более четкого отображения) по y
            **/
            if( gr.lineWidth % 2 == 0)
                xRatio = 0;
            else
                xRatio = 0.5;
            yRatio = xRatio;


            /**Коррекция координат*/
            x += xRatio;
            yH += yRatio;
            yBody += yRatio;
            yCloseBody += yRatio;
            yL += yRatio;

            /**Отрисовка верхней тени*/
            gr.beginPath();
            gr.moveTo(x, yH);
            gr.lineTo(x, yBody);
            gr.strokeStyle = this.topShadowBarColor;
            gr.closePath();
            gr.stroke();

            /**Отрисовка тела*/
            if( this.widthBar == 1 ){
                gr.beginPath();
                gr.moveTo(x, yBody);
                gr.lineTo(x, yCloseBody);
                gr.strokeStyle = bodyColor;
                gr.closePath();
                gr.stroke();
            }else
            {
                gr.beginPath();
                gr.moveTo( x - this.widthBar/2, yBody );
                gr.lineTo( x - this.widthBar/2, yCloseBody);
                gr.lineTo( x + this.widthBar/2, yCloseBody);
                gr.lineTo(x + this.widthBar/2, yBody);
                gr.strokeStyle = this.bodyBorderBarColor;
                gr.closePath();
                gr.fillStyle = bodyColor;
                gr.fill();
                gr.stroke();
            }

            /**Отрисовка нижней тени*/
            gr.beginPath();
            gr.moveTo(x, yCloseBody);
            gr.lineTo(x, yL);
            gr.strokeStyle = this.bottomShadowBarColor;
            gr.closePath();
            gr.stroke();

        },
        paintArrBars: function ( arrGrBars ){
                            var self = this;
                            arrGrBars.forEach( function( item ){
                                self.paintBar( item.x, item.yH , item.yBody, item.yCloseBody, item.yL, item.direction);
                            });
                        },
        _convertX: function (x){

                    },
        _convertY: function(y) {
                        return y+this.topIndent;
                    },
        setArrBars: function( arrBars ){
                        this.arrBars = arrBars;
                    },
        /** Конвертируем данные из массива баров в массив, для отображения на графике (массив, который будет понимать функция paintChart() )
         *
         * @param arrBars - массив для отображения на графике
         * @param newArrBars - новые данные, добавленные к прежнему массиву ( нужен для упрощения нахождения максимальной/минимальной цен на графике)
         * @param firstBarIndend - смещение первого бара по оси x
         * @returns {boolean} | Array
         */
        convertBarsToGraphic:   function( arrBars, firstBarIndent, newArrBars ){
                                    var self = this;
                                    var arrGrBars = [];
                                    if( newArrBars )
                                        this._recalculateMinMaxPrices(newArrBars);
                                    else
                                        this._recalculateMinMaxPrices(arrBars);
                                    this._recalculateYSteps();

                                    if( typeof firstBarIndent === 'undefined')
                                        firstBarIndent = 0;
                                    if(typeof this.decimalPlaces === 'undefined') {
                                        writeLog('this.decimalPlaces === "undefined');
                                        return false;
                                    }
                                    /**Разница между максимальной и минимальной ценой ( переводится в целые числа)*/
                                    var difference = Math.round(Math.pow(this.maxPriceOnChart - this.minPriceOnChart , this.decimalPlaces));

                                    if(typeof this.yPriceStep === 'undefined' || typeof this.yPxStep === 'undefined') {
                                        writeLog('this.priceStep или this.pxStep == "undefined');
                                        return false;
                                    }

                                    /**Формируем выходной массив с графическими данными для баров **/
                                    var firstX = this.widthBar/2 - firstBarIndent;
                                    var arrGrBars = [];
                                    arrBars.forEach( function( item, key, arr ){
                                        arrGrBars.push({
                                            x: firstX+(self.widthBar+self.barSpacing) * key,
                                            yH: self._convertY( Math.round( (self.maxPriceOnChart - item.high) / self.yPriceStep ) * self.yPxStep),
                                            yBody: self._convertY( Math.round( (self.maxPriceOnChart - (item.open > item.close ? item.open : item.close)) / self.yPriceStep ) * self.yPxStep),
                                            yCloseBody: self._convertY( Math.round( (self.maxPriceOnChart - (item.open < item.close ? item.open : item.close)) / self.yPriceStep ) * self.yPxStep),
                                            yL: self._convertY( Math.round( (self.maxPriceOnChart - item.low) / self.yPriceStep ) * self.yPxStep),
                                            direction: item.open >= item.close ? false : true,
                                        });
                                    });
                                    return arrGrBars;
                                },
        _recalculateMinMaxPrices:   function(arrBars){
                                        var newMin = _.min( arrBars, function( bar ) { return bar.low;   }).low;
                                        var newMax = _.max( arrBars, function( bar ) { return bar.high;   }).high;

                                        if(newMin < this.minPriceOnChart ) this.minPriceOnChart = newMin;
                                        if(newMax > this.maxPriceOnChart ) this.maxPriceOnChart = newMax;
                                    },
        _recalculateYSteps:   function(){
            /**Разница между максимальной и минимальной ценой ( переводится в целые числа)*/
            var difference = Math.round((this.maxPriceOnChart - this.minPriceOnChart) * Math.pow( 10, this.decimalPlaces));
//            console.log('difference');
//            console.log(difference);
            if( this.chartHeight >= difference) {
                this.yPriceStep = this.defaultPriceStep;
                this.yPxStep = Math.floor( this.chartHeight / difference );
            }
        },
        _findCountDecimalPlaces:    function( num ){
                                        /**Начальная точность (количество знаков после запятой)*/
                                        var countDecimalPlaces = 0;
                                        var intNum = num;
                                        while( num * Math.pow( 10, countDecimalPlaces ) % 1 !== 0 ){
                                            countDecimalPlaces++;
                                            if( countDecimalPlaces > 30){
                                                writeLog('Вычисление количества знаков после запятой у числа вышло за допустимые пределы');
                                                return false;
                                            }
                                        }
                                        return countDecimalPlaces;
                                    },

    }

</script>