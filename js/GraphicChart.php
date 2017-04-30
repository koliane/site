<script>
    /* grSettings = { barWidth: .., barSpacing: .., priceIndicatorWidth: .., timeIndicatorHeight: .., topIndent: .., bottomIndent: .. }*/

    function GrChart(width, height, grSettings, context) {
        var self = this;
//        this.namePair = grSettings
        this.arrBars = context.arrPairs[0].timeFrames[0].fullChart;

        var countOfBars = [90, 'm', 2, [2015, 5, 2, 23, 58]];
//        this.arrBars = generatePrices(countOfBars, [1.1054, 1.1058], 100);
//        this.arrBars = [{year:2015,month:5,day:3,hour:4, minute:38,open: 1.1399, high: 1.1399, low: 1.1052, close:1.1399}];
//        console.table(this.arrBars);
        this.fullArrBars = generatePrices(countOfBars, [1.1054, 1.1058], 50);

        this.arrBars = [];
        this.arrGrBars = [];
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


        /**Размеры графика в canvas( размеры области, в которой может отображаться график)*/
        this.chartWidth = this.width;
//        this.chartWidth = this.width  - this.rightIndent;
        this.chartHeight = this.height - this.topIndent - this.bottomIndent;

        this.id = grSettings.lastChartIndex;

        this.widthBar = grSettings.widthBar;
        /**Необходимо указывать четным, либо 1*/
        this.barSpacing = grSettings.barSpacing;

        /**Отступ от левого края для первого бара (может не входить полностью в canvas)*/
        this.firstBarIndent = 3;



        /**Максимальное число баров, которые могут уместиться на графике*/
        this.maxCountBarOnChart = this._recalculateMaxCountBarsOnChart();
        /**Количество баров на графике с учетом правого отступа*/
        this.countBarOnChartWithIndent = this._recalculateMaxCountBarsOnChart(true);
        /**Количество баров на графике в данный момент*/
        this.currentCountBarOnChart;

        /**Максимальная и минимальные цены на графике*/
        this.maxPriceOnChart = 0;
        this.minPriceOnChart = 9999999999;


        /**Цвета баров*/
        this.buyBarColor = grSettings.buyBarColor;
        this.sellBarColor = grSettings.sellBarColor;

        this.bodyBorderBarColor = grSettings.bodyBorderBarColor;
        this.topShadowBarColor = grSettings.topShadowBarColor;
        this.bottomShadowBarColor = grSettings.bottomShadowBarColor;
        this.textColor = grSettings.textColor;
        this.textFont = grSettings.textFont;
        this.textSize = grSettings.textSize;
        /**Коэффициент минимальной разряженности между отображенными ценами (мин. расстояние вычисляется следующим образом: РазмерШрифта * Коэффициент разряженности)*/
        this.defaultRatioPriceSpacing = grSettings.ratioPriceSpacing;
        /**Минимальное расстояние между ценами(в px)*/
        this.defaultPriceSpacing = this.textSize * this.defaultRatioPriceSpacing;
        this.currentPriceSpacing = this.defaultPriceSpacing;

        /**Минимальное расстояние между началом каждой метки времени (для отображения на графике)*/
        this.timeSpacing = Math.ceil(grSettings.timeSpacing / ( this.widthBar + this.barSpacing)) * ( this.widthBar + this.barSpacing);

        /**Количество цифр после запятой, которые учитываются в вычислениях*/
        this.decimalPlaces = grSettings.decimalPlaces;
        this.defaultPriceStep = Math.pow(10, -this.decimalPlaces);

        this.yPxStep;
        this.yPriceStep;
        this.currentRatioPriceSpacing = this.defaultRatioPriceSpacing;


        canvas = document.getElementById("canvas" + this.id);
        this.gr = canvas.getContext("2d");

        /**Получаем графический контекст для поля цены и даты*/
        canvas = document.getElementById("rightCanvas" + this.id);
        this.grRight = canvas.getContext("2d");
        canvas = document.getElementById("bottomCanvas" + this.id);
        this.grBottom = canvas.getContext("2d");


        /**Линии области графика*/
        this.gr.beginPath();
        this.gr.moveTo(-0.5, this.topIndent + 0.5);
        this.gr.lineTo(this.width + 0.5, this.topIndent + 0.5);
        this.gr.strokeStyle = 'lightgray';
        this.gr.closePath();
        this.gr.stroke();

        this.gr.beginPath();
        this.gr.moveTo(-0.5, this.topIndent + 0.5 + this.chartHeight);
        this.gr.lineTo(this.width + 0.5, this.topIndent + 0.5 + this.chartHeight);
        this.gr.closePath();
        this.gr.stroke();
        /**END Линии области графика*/

        this.grRight.strokeStyle = this.textColor;
        this.grRight.fillStyle = this.textColor;
        this.grRight.font = this.textFont;

        this.grBottom.strokeStyle = this.textColor;
        this.grBottom.fillStyle = this.textColor;
        this.grBottom.font = this.textFont;

        /**Id бара из полного массива баров(fullArrBars), с которого начинается массив для отображения на графике(arrBars)*/
//        this.idFirstBar = 1;

//        this._fillArrBars(false, 0);
//        console.table(this.fullArrBars);
        /**Индекс бара из полного массива баров(fullArrBars), с которого начинается массив для отображения на графике(arrBars)*/
        this.indexFirstBar = this.fullArrBars.length > +this.maxCountBarOnChart ? this.fullArrBars.length - +this.maxCountBarOnChart : 0;
        this._fillArrBars(  this.indexFirstBar );
//        console.table(this.arrBars);
//        console.table( this.convertBarsToGraphic(this.arrBars) );
        this.arrGrBars = this.convertBarsToGraphic(this.arrBars, this.firstBarIndent);
        this.paintArrBars( this.arrGrBars );
//        this.paintArrBars(this.convertBarsToGraphic(this.arrBars, this.firstBarIndent));
        this.paintPrices();
        this.paintTimes();
//        console.log(this._recalculateMaxCountBarsOnChart( true ));

        this.isMouseDown = false;
        this.isRightIndent = true; //Есть ли отступ справа на текущий момент

        /**Предыдущее положение курсора по оси Х*/
        this.prevMousePositionX;

        console.log( 'arrBars.size');
        console.log(this.arrBars.length);
//        this.gr.font = "bold 4px sans-serif";

        /**Обработчик нажатия левой клавиши мыши на графике*/
        $('#canvas' + this.id).mousedown( function(e){
            self.isMouseDown = true;
            self.startMousceClickX = e.pageX;
            self.prevMousePositionX = e.pageX;
        });
        $('#canvas' + this.id).mouseup( function(){
            self.isMouseDown = false;
        });
        /**Обработчик события движения мыши с зажатой левой клавишей*/
        $('#canvas' + this.id).mousemove( function(e){
            if( self.isMouseDown == true ) {
//                self._moveChart( e );

//                self.firstBarIndent = self.firstBarIndent + self.prevMousePositionX - e.pageX;
//                self.prevMousePositionX = e.pageX;
//                self.gr.clearRect( 0, 0, self.width, self.height );
//                self.paintArrBars( self.convertBarsToGraphic( self.arrBars, self.firstBarIndent ) );

//                console.log(self._recalcEndIndent());
//                console.log(self.arrGrBars[self.arrGrBars.length - 1].x );
//                self.moveChart(e);

//                console.log( 'arrBars.size');
//                console.log(self.arrBars.length);
//                console.log(self.widthBar);
            }
        });

        $( document ).keydown(function (e) {
            /*Обработка нажатия левой клавиши*/
            if(e.which == 37) {
                alert();
            }
            /*Обработка нажатия правой клавиши*/
            if(e.which == 39) {

                self._moveChartRight(20);
//                self._moveChartRight(1,'bar',{shiftFirstBar:'showFull',direction:'right'});
            }
        });
    }
    GrChart.prototype = {
        constructor: GrChart,
        /**
         * Функция отрисовки бара. Входные параметры заданы в пикселях.
         * @param x - горизонтальная координата, где будет находится середина бара
         * @param yH - вертикальная координата, где будет начинаться high бара
         * @param yBody
         * @param yCloseBody
         * @param yL
         * @direction - тип бара(от этого параметра зависит цвет заливки тела бара). Если true - бар покупки.
         */
        paintBar: function (x, yH, yBody, yCloseBody, yL, direction) {
            this.gr.fillText(x, x, this.height - 30);

            var gr = this.gr;
            var bodyColor = direction ? this.buyBarColor : this.sellBarColor;


            /* xRatio - смещение бара на определенное значение в px (для более четкого отображения) по x
             * yRatio - смещение бара на определенное значение в px (для более четкого отображения) по y
             **/
            if (gr.lineWidth % 2 == 0)
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
            if (this.widthBar == 1) {
                gr.beginPath();
                gr.moveTo(x, yBody);
                gr.lineTo(x, yCloseBody);
                gr.strokeStyle = bodyColor;
                gr.closePath();
                gr.stroke();
            } else {
                gr.beginPath();
                gr.moveTo(x - this.widthBar / 2, yBody);
                gr.lineTo(x - this.widthBar / 2, yCloseBody);
                gr.lineTo(x + this.widthBar / 2, yCloseBody);
                gr.lineTo(x + this.widthBar / 2, yBody);
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
        paintArrBars: function (arrGrBars) {
            var self = this;
            arrGrBars.forEach(function (item, key) {
                self.paintBar(item.x, item.yH, item.yBody, item.yCloseBody, item.yL, item.direction);
                self.gr.fillText(self.indexFirstBar+key, item.x, 30);
            });
        },
        paintPrices: function () {
            /**Вычислим шаг метки в px и шаг в значениях цены*/
            /**Устанавливаем шаг в пикселях, с которым будут показываться цены*/
            this.currentPriceSpacing = Math.ceil(this.defaultPriceSpacing / this.yPxStep) * this.yPxStep;
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

            for (var i = 0; i < num; i++) {
                /**Рисуем черточку перед ценой*/
                this.grRight.beginPath();
                this.grRight.moveTo(-0.5, position + 0.5);
                this.grRight.lineTo(5.5, position + 0.5);
                this.grRight.closePath();
                this.grRight.stroke();
                /**Выводим цену*/
                this.grRight.fillText('' + curPrice.toFixed(this.decimalPlaces), 10.5, position + this.textSize / 3);
                position += this.currentPriceSpacing;
                /**Если надпись снизу будет отображаться не полностью(т.е. выходить за рамки canvas), то не отображаем надпись*/
                if (position > this.height - this.textSize / 2)
                    break;
                curPrice = this._convertToFloat(this._convertToInt(curPrice - valuePricingStep, this.decimalPlaces), this.decimalPlaces);
            }

            /**Покажем цены выше максимальной цены**/
            var heightTopIndent = this.topIndent + this.topIndentRelative;
            num = Math.ceil(heightTopIndent / this.currentPriceSpacing) - 1;
            position = this.topIndent + this.topIndentRelative - this.currentPriceSpacing;

            var curPrice = this._convertToFloat(this._convertToInt(+this.maxPriceOnChart + +valuePricingStep, this.decimalPlaces), this.decimalPlaces);

            for (var i = 0; i < num; i++) {
                /**Если надпись сверху будет отображаться не полностью(т.е. выходить за рамки canvas), то не отображаем надпись*/
                if (position < this.textSize / 2)
                    break;
                this.grRight.beginPath();
                this.grRight.moveTo(-0.5, position + 0.5);
                this.grRight.lineTo(5.5, position + 0.5);
                this.grRight.closePath();
                this.grRight.stroke();

                this.grRight.fillText(curPrice.toFixed(this.decimalPlaces), 10.5, position + this.textSize / 3);
                position -= this.currentPriceSpacing;
                curPrice = this._convertToFloat(this._convertToInt(+curPrice + +valuePricingStep, this.decimalPlaces), this.decimalPlaces);
            }
        },
        paintTimes: function () {
            //ВРЕМЕННО
            var widthText = this.grBottom.measureText('mmmm.mm.mm mm.mm').width;
            var position = +this.widthBar / 2 - +this.firstBarIndent + 0.5;
            /**Индекс бара в массиве, для которого в данный момент отображается время*/
            var curBarIndex = 0;
            var textCurTime = this._formatTimeToStr(this.arrBars[curBarIndex]);
            if (position < 0) {
                position = +position + +this.widthBar + +this.barSpacing;
                curBarIndex++;
                textCurTime = this._formatTimeToStr(this.arrBars[curBarIndex]);
            }
            /**Вычислим количество баров, через которое выводится время*/
            var numBars = Math.round(this.timeSpacing / (+this.widthBar + +this.barSpacing));

            var num = Math.floor((this.chartWidth - position) / this.timeSpacing);
            for (var i = 0; i < num; i++) {
                /**Рисуем черточку над временем*/
                this.grBottom.beginPath();
                this.grBottom.moveTo(position, -0.5);
                this.grBottom.lineTo(position, 5.5);
                this.grBottom.closePath();
                this.grBottom.stroke();
                /**Выводим цену*/
//                this.grBottom.fillText( textCurTime, position - Math.round( this.textSize/3), 20.5);
                this.grBottom.fillText(textCurTime, position, 15.5);
                position += +this.timeSpacing;
                curBarIndex += numBars;
                if (curBarIndex >= this.arrBars.length)
                    break;

                textCurTime = this._formatTimeToStr(this.arrBars[curBarIndex]);
            }
        },
        /**Сдвинуть график. e - параметр, для работы с мышкой*/
        <?require_once "func_GraphicChart/moveChart.php"?>


        /**
         *  Функция сдвигает график направо, добавляя бары слева
         *  @param {int} distance - расстояние или кол-во баров, на которые необходимо сдвинуть график
         *  @param {string} type = 'px' - тип сдвига. Если type = 'px', то distance - кол-во пикселей. Если type = 'bar', то distance - кол-во бар
         *  @param {object} options - параметры сдвига. Актуально, если type == bar
         *  @param {object} options ( {shiftFirstBar: 'showFull' | 'prev', direction: 'left' | 'right' } ) - определяет, каким образом сдвигать бары (актуально только для type = 'bar').
         *  Если shiftFirstBar == 'showFull', то бары добавляются, обнуляя левый(или правый) (зависит от direction) отступ(this.firstBarIndent), иначе this.firstBarIndent сохраняется.
         **/
        _moveChartRight: function( distance, type, options ){
            /*Значения по умолчанию*/
            if( typeof distance == 'undefined' || typeof distance != 'number' || distance == 0 )
                return;
            distance = Math.abs( distance );

            var default_shiftFirstBar = 'prev';
            var default_direction = 'left';
            type = type || 'px';
            if( type == 'bar' ) {
                if (typeof options == 'undefined') {
                    options = {
                        shiftFirstBar: default_shiftFirstBar,
                        direction: default_direction
                    }
                } else {
                    if( typeof options.shiftFirstBar == 'undefined' || typeof options.shiftFirstBar !== 'string' ||
                        options.shiftFirstBar != 'showFull' && options.shiftFirstBar != 'prev' ) {
                        options.shiftFirstBar = default_shiftFirstBar;
                    }
                    if( typeof options.direction == 'undefined' || typeof options.direction !== 'string' ||
                        options.direction != 'left' && options.direction != 'right' ) {
                        options.direction = default_direction;
                    }
                }
            }
            /*--------------------*/

//            console.log('start');
//            console.log( 'distance ='+ distance);
//            console.log( 'type ='+ type);
//            console.log( 'options.shiftFirstBar ='+ options.shiftFirstBar);
//            console.log( 'options.direction ='+ options.direction);

            if( type == 'bar' ){
                if (options.shiftFirstBar == 'prev' ) {
                    if (this.indexFirstBar - distance < 0) {
                        this.indexFirstBar = 0;
                        this.firstBarIndent = 0;
                    } else
                        this.indexFirstBar -= distance;
                } else
                if( options.direction == 'left' ) {
                    if (options.shiftFirstBar == 'showFull') {

                        if (this.firstBarIndent > 0 && this.indexFirstBar - distance + 1 < 0 ||
                            this.firstBarIndent <= 0 && this.indexFirstBar - distance < 0
                        ) {
                            this.indexFirstBar = 0;
                        } else if (this.firstBarIndent > 0)
                            this.indexFirstBar -= distance - 1;
                        else
                            this.indexFirstBar -= distance;
                        this.firstBarIndent = 0;
                    }
                }
                else
                if( options.direction == 'right' ) {
                    if (options.shiftFirstBar == 'showFull') {
                        /*Если есть правый отступ*/
                        if( this._recalcEndIndent() < 0 && Math.abs( this._recalcEndIndent() ) - (distance * (this.widthBar + this.barSpacing)) > 0){
                            if (this.indexFirstBar - distance < 0) {
                                this.indexFirstBar = 0;
                                this.firstBarIndent = 0;
                            } else
                                this.indexFirstBar -= distance;
                        }else
                        if (this._recalcEndIndent() > 0 && this.indexFirstBar - distance + 1 < 0 ||
                            this._recalcEndIndent() <= 0 && this.indexFirstBar - distance < 0
                        ) {
                            this.indexFirstBar = 0;
//                            this.firstBarIndent = 0;

                        } else if (this._recalcEndIndent() < 0) {
                            this.indexFirstBar -= distance - 1;
                            this.firstBarIndent += this._recalcEndIndent();

                        }
                        else
                        {
                            this.indexFirstBar -= distance;
                            this.firstBarIndent += this._recalcEndIndent();

                        }

                        if(  this.indexFirstBar === 0 && this.firstBarIndent < 0 )
                            this.firstBarIndent = 0;
                    }
                }
            }
            /*Если type='px'*/
            else {
                /*Узнаем сколько баров умещается в пространство слева*/
                var numBarsInLeftIndent = Math.floor( Math.abs(distance - this.firstBarIndent + this.widthBar )/(this.widthBar + this.barSpacing) ) ;
//
                if( this.indexFirstBar - numBarsInLeftIndent < 0){
                    this.indexFirstBar = 0;
                    this.firstBarIndent = 0;
                }else{
                    this.indexFirstBar -= numBarsInLeftIndent;
//                    console.log('gg');
//                    console.log('numBarsInLeftIndent'+numBarsInLeftIndent);
//                    console.log(distance - this.firstBarIndent + this.widthBar - 1);
                    if( numBarsInLeftIndent === 0 ) {
//                        if( this.firstBarIndent <=0 )
                            this.firstBarIndent -= distance;
//                        else
//                            this.firstBarIndent += distance;

                    }
                    else {
                        this.firstBarIndent = this.widthBar - Math.floor( Math.abs(distance - this.firstBarIndent + this.widthBar )/(this.widthBar + this.barSpacing) );
//                        console.log('indent='+this.firstBarIndent);
                    }
                }

//                console.log(distance);
//                console.log('indent='+this.firstBarIndent);
////                console.log((this.widthBar + this.barSpacing));
//                console.log( 'num='+numBarsInLeftIndent);
            }

            this._fillArrBars( this.indexFirstBar );
            this.gr.clearRect( 0, 0, this.width, this.height );
            this.paintArrBars( this.convertBarsToGraphic( this.arrBars, this.firstBarIndent ) );
        },
        /**Высчитываем расстояние от правой границы canvas до самого правого бара графика.
         * Если вплотную к границе, то = 0. Если выходит за рамки, то > 0, иначе < 0*/
        _recalcEndIndent: function(){
            return -( this.width - (this.arrBars.length*( +this.widthBar + +this.barSpacing) - this.barSpacing - this.firstBarIndent ) - 1 );
        },
        _formatTimeToStr: function (bar) {
            var strMonth = '';
            switch (bar.month) {
                case 1:
                    strMonth = 'Jan';
                    break;
                case 2:
                    strMonth = 'Feb';
                    break;
                case 3:
                    strMonth = 'Mar';
                    break;
                case 4:
                    strMonth = 'Apr';
                    break;
                case 5:
                    strMonth = 'May';
                    break;
                case 6:
                    strMonth = 'Jun';
                    break;
                case 7:
                    strMonth = 'Jul';
                    break;
                case 8:
                    strMonth = 'Aug';
                    break;
                case 9:
                    strMonth = 'Sep';
                    break;
                case 10:
                    strMonth = 'Oct';
                    break;
                case 11:
                    strMonth = 'Nov';
                    break;
                case 12:
                    strMonth = 'Dec';
                    break;
            }

            str = '' + bar.year + '-' + strMonth + '-' + this._formatNumber(bar.day, 2) + ' ' +
                this._formatNumber(bar.hour, 2) + ':' + this._formatNumber(bar.minute, 2);
            return str;
        },
        /**
         * Форматирует число, добавляя ведущие нули, если необходимо
         * @param value - форматируемое число
         * @param numPlaces - количество цифр для отображения
         * @return - строка, где число выводится с необходимым количеством ведущих нулей
         */
        _formatNumber: function (value, numPlaces) {
            str = '';
            if (typeof numPlaces === 'undefined')
                numPlaces = 2;

            if (value < Math.pow(10, numPlaces)) {
                /**Вычисляем количество нулей для добавления*/
                var numZeros = numPlaces - ('' + value).length;

                for (var i = 0; i < numZeros; i++)
                    str += '0';
            }
            str += value;
            return str;
        },

        /**Сдвинуть вертикальную графическую координату на отступ сверху*/
        _convertY: function (y) {
            return y + this.topIndent + this.topIndentRelative;
        },
        setArrBars: function (arrBars) {
            this.arrBars = arrBars;
        },
        /** Конвертируем данные из массива баров в массив, для отображения на графике (массив, который будет понимать функция paintChart() )
         *
         * @param arrBars - массив для отображения на графике
         * @param newArrBars - новые данные, добавленные к прежнему массиву ( нужен для упрощения нахождения максимальной/минимальной цен на графике)
         * @param firstBarIndend - смещение первого бара по оси x
         * @returns {boolean} | Array
         */
        convertBarsToGraphic: function (arrBars, firstBarIndent) {
            if (typeof firstBarIndent === 'undefined')
                firstBarIndent = 0;
            if (typeof this.decimalPlaces === 'undefined') {
                writeLog('this.decimalPlaces === "undefined');
                return false;
            }
            var self = this;
            var arrGrBars = [];
            this._recalculateMinMaxPrices(arrBars);
            this._recalculateYSteps();
            this._recalculateTopIndentRelative();

//            this.maxCountBarOnChart = this._recalculateMaxCountBarsOnChart( this.isRightIndent );
            /**Разница между максимальной и минимальной ценой ( переводится в целые числа)*/
//                                    var difference = Math.round(Math.pow(this.maxPriceOnChart - this.minPriceOnChart , this.decimalPlaces));

            if (typeof this.yPriceStep === 'undefined' || typeof this.yPxStep === 'undefined') {
                writeLog('this.priceStep или this.pxStep == "undefined');
                return false;
            }

            /**Формируем выходной массив с графическими данными для баров **/
            var firstX = this.widthBar / 2 - firstBarIndent;
            var arrGrBars = [];
            var x, yH, yBody, yCloseBody, yL, direction;



            arrBars.forEach(function (item, key, arr) {
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
            this.arrGrBars = arrGrBars;
            return arrGrBars;
        },
        _recalculateMinMaxPrices: function (arrBars) {
            var newMin = _.min(arrBars, function (bar) {
                return bar.low;
            }).low;
            var newMax = _.max(arrBars, function (bar) {
                return bar.high;
            }).high;

            if (newMin < this.minPriceOnChart) this.minPriceOnChart = newMin;
            if (newMax > this.maxPriceOnChart) this.maxPriceOnChart = newMax;
        },
        _recalculateYSteps: function () {
            /**Разница между максимальной и минимальной ценой ( переводится в целые числа)*/
            var difference = Math.round((this.maxPriceOnChart - this.minPriceOnChart) * Math.pow(10, this.decimalPlaces));
            if (this.chartHeight >= difference) {
                this.yPriceStep = this.defaultPriceStep;
                if (difference != 0)
                    this.yPxStep = Math.floor(this.chartHeight / difference);
                else
                    this.yPxStep = 1;
            } else {
                if (Math.round(((difference / this.chartHeight) % 1) * 10) < 5) {
                    this.yPxStep = 2;
                    this.yPriceStep = this._convertToFloat(Math.floor(difference / this.chartHeight) * 3, this.decimalPlaces);
                }
                else {
                    this.yPxStep = 1;
                    this.yPriceStep = this._convertToFloat(Math.ceil(difference / this.chartHeight), this.decimalPlaces);
                }

            }
//            console.log('difference='+difference+' chartHeight='+this.chartHeight);
//            console.log('yPxStep='+this.yPxStep+' yPriceStep='+this.yPriceStep)
        },
        /**Располагаем график по середине (по вертикали)*/
        _recalculateTopIndentRelative: function () {
            /**Разница между максимальной и минимальной ценой ( переводится в целые числа)*/
            var difference = Math.round((this.maxPriceOnChart - this.minPriceOnChart) * Math.pow(10, this.decimalPlaces));
            if (this.chartHeight >= difference) {
                if (difference != 0)
                    this.topIndentRelative = Math.floor((this.chartHeight % difference) / 2);
                else
                    this.topIndentRelative = Math.floor((this.chartHeight ) / 2);
            } else {
                this.topIndentRelative = Math.floor((this.chartHeight - difference / this._convertToInt(this.yPriceStep, this.decimalPlaces) * this.yPxStep) / 2);
            }
//                                            console.log('topIndentRelative='+this.topIndentRelative);
        },

        _findCountDecimalPlaces: function (num) {
            /**Начальная точность (количество знаков после запятой)*/
            var countDecimalPlaces = 0;
            var intNum = num;
            while (num * Math.pow(10, countDecimalPlaces) % 1 !== 0) {
                countDecimalPlaces++;
                if (countDecimalPlaces > 30) {
                    writeLog('Вычисление количества знаков после запятой у числа вышло за допустимые пределы');
                    return false;
                }
            }
            return countDecimalPlaces;
        },
        /**Преобразует целое число, к числу с плавающей запятой, деля  его на число 10^decimalPlaces(т.е. сдвигая его на определенное кол-во разрядов)*/
        _convertToInt: function (num, decimalPlaces) {
            decimalPlaces = decimalPlaces || 4;
            return Math.round(num * Math.pow(10, decimalPlaces));
        },
        /**Преобразует число с плавающей запятой, к целому числу, умножая его на число 10^-decimalPlaces(т.е. сдвигая его на определенное кол-во разрядов)*/

        _convertToFloat: function (num, decimalPlaces) {
            decimalPlaces = decimalPlaces || 4;
            return +(num * Math.pow(10, -decimalPlaces)).toFixed(decimalPlaces);
        },
        /** Узнать кол-во баров, способных уместиться в canvas
         * @param isRightIndent Bool - есть ли отступ справа.
         * */
        _recalculateMaxCountBarsOnChart: function( isRightIndent, fBarIndent ){
            if( isRightIndent ){
                var workWidth = this.width - this.rightIndent;
            }else{
                var workWidth = this.width
            }

            if( typeof fBarIndent == 'undefined' )
                var resIndent = this.firstBarIndent;
            else
                var resIndent = fBarIndent;

//            return Math.ceil( (workWidth + this.firstBarIndent) / ( this.widthBar + this.barSpacing ) );
            return Math.ceil( (workWidth + resIndent) / ( this.widthBar + this.barSpacing ) );
        },
        /**Заполнить массив arrBars соответствующими барами из fullArrBars
         * @param isRightIndent Bool - Есть ли правый отступ (только для начала графика)
         * @param index Int - Добавить бары, начиная с index элемента в fullArrBars*/
        _fillArrBars: function(  index, isRightIndent ){
//        _fillArrBars: function( isRightIndent, index ){
//            if( typeof fBarIndent == 'undefined' || fullArrBars.length > 0) {
            if( typeof isRightIndent == 'undefined' )
                isRightIndent = false;
            if( typeof this.fullArrBars != 'undefined' && typeof this.fullArrBars.length != 'undefined' && this.fullArrBars.length > 0) {

                if (isRightIndent) {
                    this.countBarOnChartWithIndent = this._recalculateMaxCountBarsOnChart(true);
                    var countBar = this.countBarOnChartWithIndent;
                    index = this.fullArrBars.length - countBar;
                }
                else
                    var countBar = this.maxCountBarOnChart;

                if (isRightIndent == false && typeof index == 'undefined' || index < 0) {
                    index = 0;
                }

                this.indexFirstBar = index;
                this.idFirstBar = this.fullArrBars[index].id;
                this.arrBars = this.fullArrBars.slice(index, +index + +countBar);

            }else
                writeLog('Массив fullArrBars пуст или не объявлен');
        },

    }



</script>