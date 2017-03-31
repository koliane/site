<script>
    /* grSettings = { barWidth: .., barSpacing: .., priceIndicatorWidth: .., timeIndicatorHeight: .., topIndent: .., bottomIndent: .. }*/

    function GrChart(width, height, grSettings, context) {
        var self = this;
        this.namePair = grSettings

        this.arrBars = context.arrPairs[0].timeFrames[0].fullChart;

        var countOfBars = [20,'m',2,[2015,5,2,23,58]];
//        this.arrBars = generatePrices(countOfBars,[1.1050,1.1380], 100);
        this.arrBars = generatePrices(countOfBars,[1.1054,1.1058], 100);
//        this.arrBars = [{year:2015,month:5,day:3,hour:4, minute:38,open: 1.1399, high: 1.1399, low: 1.1052, close:1.1399}];
        console.table(this.arrBars );
//        this.arrGrBars = [];
        /**Размеры canvas*/
        this.width = width;
        this.height = height;


        /**Отступы (аналогично padding, только для графика в canvas)*/
        this.topIndent = grSettings.topIndent;
        this.bottomIndent = grSettings.bottomIndent || grSettings.topIndent;
//        this.leftIndent = grSettings.leftIndent;
        this.rightIndent = grSettings.rightIndent;

        /** + Дополнительный отступ сверху для отображения графика по середине*/
        this.topIndentRelative = 0;

//        this.priceIndicatorWidth = grSettings.priceIndicatorWidth;
//        this.timeIndicatorHeight = grSettings.timeIndicatorHeight;

        /**Размеры canvas графика(котировок)*/
//        this.workWidth = width - grSettings.priceIndicatorWidth;
//        this.workHeight = height - grSettings.timeIndicatorHeight;

        /**Размеры графика в canvas( размеры области, в которой может отображаться график)*/
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
        this.firstBarIndent = 0;

        /**Цвета баров*/
        this.buyBarColor = grSettings.buyBarColor;
        this.sellBarColor = grSettings.sellBarColor;

        this.bodyBorderBarColor =  grSettings.bodyBorderBarColor;
        this.topShadowBarColor =  grSettings.topShadowBarColor;
        this.bottomShadowBarColor =  grSettings.bottomShadowBarColor;
        this.textColor = grSettings.textColor;
        this.textFont = grSettings.textFont;
        this.textSize = grSettings.textSize;
        /**Коэффициент минимальной разряженности между отображенными ценами (мин. расстояние вычисляется следующим образом: РазмерШрифта * Коэффициент разряженности)*/
        this.defaultRatioPriceSpacing = grSettings.ratioPriceSpacing;
        /**Минимальное расстояние между ценами(в px)*/
        this.defaultPriceSpacing = this.textSize * this.defaultRatioPriceSpacing;
        this.currentPriceSpacing = this.defaultPriceSpacing;

        /**Минимальное расстояние между началом каждого времени (для отображения на графике)*/
        this.timeSpacing = Math.ceil( grSettings.timeSpacing / ( this.widthBar + this.barSpacing) )*( this.widthBar + this.barSpacing);

        /**Количество цифр после запятой, которые учитываются в вычислениях*/
        this.decimalPlaces = grSettings.decimalPlaces;
        this.defaultPriceStep = Math.pow(10, -this.decimalPlaces);

        this.yPxStep;
        this.yPriceStep;
        this.currentRatioPriceSpacing = this.defaultRatioPriceSpacing;




        canvas = document.getElementById("canvas"+this.id);
        this.gr = canvas.getContext("2d");

        /**Получаем графический контекст для поля цены и даты*/
        canvas = document.getElementById("rightCanvas"+this.id);
        this.grRight = canvas.getContext("2d");
        canvas = document.getElementById("bottomCanvas"+this.id);
        this.grBottom = canvas.getContext("2d");


        /**Линии области графика*/
        this.gr.beginPath();
        this.gr.moveTo(-0.5, this.topIndent+0.5);
        this.gr.lineTo(this.width+0.5, this.topIndent+0.5);
        this.gr.strokeStyle = 'lightgray';
        this.gr.closePath();
        this.gr.stroke();

        this.gr.beginPath();
        this.gr.moveTo(-0.5, this.topIndent+0.5+this.chartHeight);
        this.gr.lineTo(this.width+0.5, this.topIndent+0.5+this.chartHeight);
        this.gr.closePath();
        this.gr.stroke();
        /**END Линии области графика*/

        this.grRight.strokeStyle = this.textColor;
        this.grRight.fillStyle = this.textColor;
        this.grRight.font = this.textFont;

        this.grBottom.strokeStyle = this.textColor;
        this.grBottom.fillStyle = this.textColor;
        this.grBottom.font = this.textFont;

//        console.table( this.convertBarsToGraphic(this.arrBars) );
        this.paintArrBars(this.convertBarsToGraphic(this.arrBars));
//        this.paintArrBars(this.convertBarsToGraphic([{year:2015,month:5,day:3,hour:4, minute:38,open: 1.1399, high: 1.1399, low: 1.1052, close:1.1399}]));
        this.paintPrices();
        this.paintTimes();

    }
    GrChart.prototype = {
        constructor: GrChart,
        paint: function( arrGrBars, leftBarIndent ){
                    this.paintBar( 100, 50, 100, 110, 200, true);
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

        paintPrices: function (){
            /**Вычислим шаг метки в px и шаг в значениях цены*/
            /**Устанавливаем шаг в пикселях, с которым будут показываться цены*/
            this.currentPriceSpacing =  Math.ceil( this.defaultPriceSpacing / this.yPxStep ) * this.yPxStep;
            /**Шаг цен меток в значениях цены. Например, 0.0072*/
            var valuePricingStep = ( this.currentPriceSpacing / this.yPxStep * this.yPriceStep).toFixed(this.decimalPlaces);
            /*-END-----------------------------*/

            /**Высота canvas без верхнего отступа*/
            var heightWithoutTopIndent = this.height - this.topIndent - this.topIndentRelative;
            /**Количество меток для отображения*/
            var num = Math.ceil(heightWithoutTopIndent / this.currentPriceSpacing);
            /**Текущая позиция метки в px*/
            var position = this.topIndent + this.topIndentRelative;
            /**Текущая цена метки (для отображения)*/
            var curPrice = this.maxPriceOnChart;

            for(var i=0; i< num; i++){
                /**Рисуем черточку перед ценой*/
                this.grRight.beginPath();
                this.grRight.moveTo(-0.5, position+0.5 );
                this.grRight.lineTo(5.5, position+0.5 );
                this.grRight.closePath();
                this.grRight.stroke();
                /**Выводим цену*/
                this.grRight.fillText(''+curPrice.toFixed(this.decimalPlaces), 10.5, position+this.textSize / 3);
                position += this.currentPriceSpacing;
                /**Если надпись снизу будет отображаться не полностью(т.е. выходить за рамки canvas), то не отображаем надпись*/
                if(position > this.height - this.textSize/2)
                    break;
                curPrice = this._convertToFloat(this._convertToInt(curPrice - valuePricingStep, this.decimalPlaces), this.decimalPlaces);
            }

            /**Покажем цены выше максимальной цены**/
            var heightTopIndent = this.topIndent + this.topIndentRelative;
            num = Math.ceil(heightTopIndent / this.currentPriceSpacing) - 1;
            position = this.topIndent + this.topIndentRelative - this.currentPriceSpacing;

            var curPrice = this._convertToFloat(this._convertToInt(+this.maxPriceOnChart + +valuePricingStep, this.decimalPlaces), this.decimalPlaces);

            for(var i=0; i< num; i++){
                /**Если надпись сверху будет отображаться не полностью(т.е. выходить за рамки canvas), то не отображаем надпись*/
                if(position <  this.textSize/2)
                    break;
                this.grRight.beginPath();
                this.grRight.moveTo(-0.5, position+0.5 );
                this.grRight.lineTo(5.5, position+0.5 );
                this.grRight.closePath();
                this.grRight.stroke();

                this.grRight.fillText(curPrice.toFixed(this.decimalPlaces), 10.5, position + this.textSize / 3);
                position -= this.currentPriceSpacing;
                curPrice = this._convertToFloat(this._convertToInt(+curPrice + +valuePricingStep, this.decimalPlaces), this.decimalPlaces);
            }
        },
        paintTimes: function(){
            //ВРЕМЕННО
            var widthText = this.grBottom.measureText('mmmm.mm.mm mm.mm').width;
            var position = +this.widthBar/2 - +this.firstBarIndent + 0.5;
            /**Индекс бара в массиве, для которого в данный момент отображается время*/
            var curBarIndex = 0;
            var textCurTime = this._formatTimeToStr(this.arrBars[curBarIndex]);
            if( position < 0 ) {
                position = +position + +this.widthBar + +this.barSpacing ;
                curBarIndex++;
                textCurTime = this._formatTimeToStr( this.arrBars[curBarIndex] );
            }
            /**Вычислим количество баров, через которое выводится время*/
            var numBars = Math.round( this.timeSpacing / (+this.widthBar + +this.barSpacing) );

            var num = Math.floor( (this.chartWidth - position) / this.timeSpacing );
            for(var i=0; i< num; i++){
                /**Рисуем черточку над временем*/
                this.grBottom.beginPath();
                this.grBottom.moveTo(position, -0.5 );
                this.grBottom.lineTo(position, 5.5 );
                this.grBottom.closePath();
                this.grBottom.stroke();
                /**Выводим цену*/
//                this.grBottom.fillText( textCurTime, position - Math.round( this.textSize/3), 20.5);
                this.grBottom.fillText( textCurTime, position , 15.5);
                position += +this.timeSpacing;
                curBarIndex += numBars;
                if( curBarIndex >= this.arrBars.length )
                    break;

                textCurTime = this._formatTimeToStr(this.arrBars[curBarIndex]);
            }
        },
        _formatTimeToStr: function(bar){
            var strMonth ='';
            switch(bar.month){
                case 1: strMonth = 'Jan';
                        break;
                case 2: strMonth = 'Feb';
                        break;
                case 3: strMonth = 'Mar';
                        break;
                case 4: strMonth = 'Apr';
                        break;
                case 5: strMonth = 'May';
                        break;
                case 6: strMonth = 'Jun';
                        break;
                case 7: strMonth = 'Jul';
                        break;
                case 8: strMonth = 'Aug';
                        break;
                case 9: strMonth = 'Sep';
                        break;
                case 10: strMonth = 'Oct';
                        break;
                case 11: strMonth = 'Nov';
                        break;
                case 12: strMonth = 'Dec';
                        break;
            }

            str =   '' + bar.year + '-' + strMonth + '-' + this._formatNumber( bar.day, 2) + ' ' +
                this._formatNumber( bar.hour, 2) + ':' +this._formatNumber( bar.minute, 2);
            return str;
        },
        /**
         * Форматирует число, добавляя ведущие нули, если необходимо
         * @param value - форматируемое число
         * @param numPlaces - количество цифр для отображения
         * @return - строка, где число выводится с необходимым количеством ведущих нулей
         */
        _formatNumber: function (value, numPlaces){
            str = '';
            if( typeof numPlaces === 'undefined')
                numPlaces = 2;

            if( value < Math.pow(10, numPlaces)){
                /**Вычисляем количество нулей для добавления*/
                var numZeros = numPlaces - (''+value).length;

                for(var i=0; i< numZeros; i++)
                    str += '0';
            }
            str += value;
            return str;
        },
        _convertX: function (x){

                    },
        /**Сдвинуть вертикальную графическую координату на отступ сверху*/
        _convertY: function(y) {
                        return y+this.topIndent + this.topIndentRelative;
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
        convertBarsToGraphic:   function( arrBars, firstBarIndent ){
                                    if( typeof firstBarIndent === 'undefined')
                                        firstBarIndent = 0;
                                    if(typeof this.decimalPlaces === 'undefined') {
                                        writeLog('this.decimalPlaces === "undefined');
                                        return false;
                                    }
                                    var self = this;
                                    var arrGrBars = [];
                                    this._recalculateMinMaxPrices(arrBars);
                                    this._recalculateYSteps();
                                    this._recalculateTopIndentRelative();
                                    /**Разница между максимальной и минимальной ценой ( переводится в целые числа)*/
//                                    var difference = Math.round(Math.pow(this.maxPriceOnChart - this.minPriceOnChart , this.decimalPlaces));

                                    if(typeof this.yPriceStep === 'undefined' || typeof this.yPxStep === 'undefined') {
                                        writeLog('this.priceStep или this.pxStep == "undefined');
                                        return false;
                                    }

                                    /**Формируем выходной массив с графическими данными для баров **/
                                    var firstX = this.widthBar/2 - firstBarIndent;
                                    var arrGrBars = [];
                                    var x, yH, yBody, yCloseBody, yL, direction;

                                    arrBars.forEach( function( item, key, arr ){
                                        x = firstX + (self.widthBar + self.barSpacing) * key;
                                        yH = self._convertY(Math.round((self.maxPriceOnChart - item.high) / self.yPriceStep) * self.yPxStep);
                                        yBody = self._convertY(Math.round((self.maxPriceOnChart - (item.open > item.close ? item.open : item.close)) / self.yPriceStep) * self.yPxStep);
                                        yCloseBody = self._convertY(Math.round((self.maxPriceOnChart - (item.open < item.close ? item.open : item.close)) / self.yPriceStep) * self.yPxStep);
                                        yL = self._convertY(Math.round((self.maxPriceOnChart - item.low) / self.yPriceStep) * self.yPxStep);
                                        direction = item.open >= item.close ? false : true;
//
                                        arrGrBars.push({
                                            x: x,
                                            yH: yH,
                                            yBody: yBody,
                                            yCloseBody: yCloseBody,
                                            yL: yL,
                                            direction: direction,
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
            if( this.chartHeight >= difference) {
                this.yPriceStep = this.defaultPriceStep;
                if(difference != 0 )
                    this.yPxStep = Math.floor( this.chartHeight / difference );
                else
                    this.yPxStep = 1;
            }else{
                if( Math.round( ((difference / this.chartHeight)%1)*10 ) < 5) {
                    this.yPxStep = 2;
                    this.yPriceStep = this._convertToFloat( Math.floor(difference / this.chartHeight) * 3, this.decimalPlaces);
                }
                else {
                    this.yPxStep = 1;
                    this.yPriceStep = this._convertToFloat( Math.ceil(difference / this.chartHeight), this.decimalPlaces);
                }

            }
//            console.log('difference='+difference+' chartHeight='+this.chartHeight);
//            console.log('yPxStep='+this.yPxStep+' yPriceStep='+this.yPriceStep)
        },
        /**Располагаем график по середине (по вертикали)*/
        _recalculateTopIndentRelative: function(){
                                            /**Разница между максимальной и минимальной ценой ( переводится в целые числа)*/
                                            var difference = Math.round((this.maxPriceOnChart - this.minPriceOnChart) * Math.pow( 10, this.decimalPlaces));
                                            if( this.chartHeight >= difference) {
                                                if( difference != 0)
                                                    this.topIndentRelative = Math.floor( (this.chartHeight % difference) / 2 );
                                                else
                                                    this.topIndentRelative = Math.floor( (this.chartHeight ) / 2 );
                                            }else{
                                                this.topIndentRelative = Math.floor((this.chartHeight - difference / this._convertToInt(this.yPriceStep, this.decimalPlaces) * this.yPxStep) / 2 );
                                            }
//                                            console.log('topIndentRelative='+this.topIndentRelative);
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
        _convertToInt:   function ( num , decimalPlaces){
                            decimalPlaces = decimalPlaces || 4;
                            return Math.round(num * Math.pow(10, decimalPlaces));
                        },
        _convertToFloat:    function( num , decimalPlaces){
                                decimalPlaces = decimalPlaces || 4;
                                return +(num * Math.pow(10, -decimalPlaces)).toFixed(decimalPlaces);
                            },

    }

</script>