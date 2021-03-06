<script>
    /* grSettings = { barWidth: .., barSpacing: .., priceIndicatorWidth: .., timeIndicatorHeight: .., topIndent: .., bottomIndent: .. }*/

    function GrChart(width, height, grSettings, context) {
        var self = this;
		this.namePair = grSettings.pairName;
		this.nameTimeframe = grSettings.timeframe;

        this.fullArrBars = context.arrPairs[0].timeFrames[0].fullChart;

        // var countOfBars = [90, 'm', 2, [2015, 5, 2, 23, 58]];
        // this.fullArrBars = generatePrices(countOfBars, [1.1054, 1.1058], 50);

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
        this.firstBarIndent = 0;



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


//        this.tempPaint();

        this.grRight.strokeStyle = this.textColor;
        this.grRight.fillStyle = this.textColor;
        this.grRight.font = this.textFont;

        this.grBottom.strokeStyle = this.textColor;
        this.grBottom.fillStyle = this.textColor;
        this.grBottom.font = this.textFont;


//        this._fillArrBars(false, 0);
//        console.table(this.fullArrBars);
        /**Индекс бара из полного массива баров(fullArrBars), с которого начинается массив для отображения на графике(arrBars)*/
        this.indexFirstBar = this.fullArrBars.length > +this.maxCountBarOnChart ? this.fullArrBars.length - +this.maxCountBarOnChart : 0;
        this._fillArrBars(  this.indexFirstBar );
//        console.table( this.convertBarsToGraphic(this.arrBars) );
        this.arrGrBars = this.convertBarsToGraphic(this.arrBars, this.firstBarIndent);
       


        this.isMouseDown = false;
        this.isRightIndent = false; //Есть ли отступ справа на текущий момент

        /**Предыдущее положение курсора по оси Х*/
        this.prevMousePositionX;

        // console.log( 'arrBars.size');
        // console.log(this.arrBars.length);
//        this.gr.font = "bold 4px sans-serif";

        this.summPxForStep = 0;
        /**Обработчик нажатия левой клавиши мыши на графике*/
        $('#canvas' + this.id).mousedown( function(e){
            self.isMouseDown = true;
            self.startMousceClickX = e.pageX;
            self.prevMousePositionX = e.pageX;
            self.summPxForStep = 0;



            if( self.modeDrawForSeek ){
				var offset = $(this).offset();
				self.indexActiveRect = self.selectRectForSeeking( e.pageX - offset.left, e.pageY - offset.top);
				self.showRectSettings();
				self.repaintRetcsForSeeking();
            }
        });
        $('#canvas' + this.id).mouseup( function(){
            self.isMouseDown = false;
        });
        /**Обработчик события движения мыши с зажатой левой клавишей*/
        $('#canvas' + this.id).mousemove( function(e){
			
			/*Отобразим OHLC*/
			if( !self.modeDrawForSeek ){
				var offset = $(this).offset();
				var eX = e.pageX - offset.left;
				var indexBarHover = self._knowIdBarHoverMouse(eX);
				if( indexBarHover !== false )
					$('.indicator-OHLC').text(	self.arrBars[indexBarHover].year + '.'+ self._formatNumber( self.arrBars[indexBarHover].month ) + '.' + self._formatNumber( self.arrBars[indexBarHover].day) + ' '
												+ self._formatNumber( self.arrBars[indexBarHover].hour ) + ':'+ self._formatNumber( self.arrBars[indexBarHover].minute ) +'    '
												+ ' O: '+self.arrBars[indexBarHover].open + '  H: '+self.arrBars[indexBarHover].high + '  L: '+self.arrBars[indexBarHover].low+ '  C: '+self.arrBars[indexBarHover].close+ '  V: '+self.arrBars[indexBarHover].volume);
				else
					$('.indicator-OHLC').text('');
			}
			/*--------------*/
			
            if( self.isMouseDown == true && !self.modeDrawForSeek) {
                self.summPxForStep -= self.prevMousePositionX - e.pageX;

                var numSteps =  ~~( self.summPxForStep/(self.widthBar + self.barSpacing) );

                if ( Math.abs( numSteps ) > 0) {
                    self.moveChart(numSteps);
                    self.summPxForStep = 0;
                }
                self.prevMousePositionX = e.pageX;

            }
        });

        $( document ).keydown(function (e) {
			if( $('.windows').is( ":hover" )){
				/*Обработка нажатия левой клавиши*/
				if(e.which == 37) {
					self._moveChartLeft( 1,'bar',{ shiftFirstBar:'prev' } );
				}
				/*Обработка нажатия правой клавиши*/
				if(e.which == 39) {
					self._moveChartRight( 1,'bar',{ shiftFirstBar:'prev' } );
				}
			}
        });


        /************************************************************************************************************/
        /************************************************************************************************************/
        /************************************************************************************************************/


        this.modeDrawForSeek = false;
		
		this.widthBlockForSeeking = 70;
		this.heightBlockForSeeking = 70;
		this.spacingBlockForSeeking = 30;
		<?require_once "func_GraphicChart/editor_construct.php"?>

        $('.draw-figure-for-seeking').click( function(){
			if( (self.arrRectForSeeking.length +1) * ( self.spacingBlockForSeeking + self.widthBlockForSeeking ) < self.width ){
				self.indexActiveRect = self.arrRectForSeeking.length;
				// self.drawRectForSeeking( self.spacingBlockForSeeking + ( self.spacingBlockForSeeking + self.widthBlockForSeeking )*self.arrRectForSeeking.length, self.height/2 -self.heightBlockForSeeking/2, self.widthBlockForSeeking, self.heightBlockForSeeking);
				self.arrRectForSeeking.push( new RectForSeeking('rect_'+self.indexActiveRect) );
				self.showRectSettings();
				self.repaintRetcsForSeeking();
			}
			// console.table(self.arrRectForSeeking);
		
        });
        $('.mode-editor').click( function(){
            self.modeDrawForSeek = !self.modeDrawForSeek;
            if( self.modeDrawForSeek ){
                $('.draw-figure-for-seeking').css('display','inline-block');
                $('.send-seek-button').css('display','inline-block');
				// self.repaintRetcsForSeeking();
			}
            else{
                $('.draw-figure-for-seeking').css('display','none');
                $('.send-seek-button').css('display','none');
			}
            self.repaint();
        });


        this.repaint();
		
    }
    GrChart.prototype = {
        constructor: GrChart,


        <?require_once "func_GraphicChart/repaint.php"?>
        /**Сдвинуть график. */
        <?require_once "func_GraphicChart/moveChart.php"?>
		
		<?require_once "func_GraphicChart/editor_prototype.php"?>

        moveChart: function( numSteps, direction ){
            if( numSteps == 0 )
                return;

            if( typeof direction == 'undefined' ){
                if( numSteps > 0 )
                    this._moveChartRight( numSteps,'bar',{shiftFirstBar:'prev'} );
                else
                    this._moveChartLeft( numSteps,'bar',{shiftFirstBar:'prev'} );
            }else{
                if( direction == 'left' )
                    this._moveChartLeft( Math.abs( numSteps ),'bar',{shiftFirstBar:'prev'} );
                else
                    this._moveChartRight( Math.abs( numSteps ),'bar',{shiftFirstBar:'prev'} );
            }
        },
        tempPaint: function(){
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


            /*rightIndent*/
            this.gr.beginPath();
            this.gr.moveTo(this.width-this.rightIndent+0.5, -0.5);
            this.gr.lineTo(this.width-this.rightIndent+0.5, 0.5+ this.height);
            this.gr.closePath();
            this.gr.stroke();
            /**END Линии области графика*/

            /*rightIndent*/
            this.gr.beginPath();
            this.gr.moveTo(this.width-this.rightIndent+0.5, -0.5);
            this.gr.lineTo(this.width-this.rightIndent+0.5, 0.5+ this.height);
            this.gr.closePath();
            this.gr.stroke();
        },


        /**Высчитываем расстояние от правой границы canvas до самого правого бара графика.
         * Если вплотную к границе, то = 0. Если выходит за рамки, то > 0, иначе < 0*/
        _recalcEndIndent: function(){
            var res = -( this.width - (this.arrBars.length*( +this.widthBar + +this.barSpacing) - this.barSpacing - this.firstBarIndent ) - 1 );
            if( Math.abs(res) >= this.widthBar + this.barSpacing && !this.isRightIndent  ) {
                res = res % (this.widthBar + this.barSpacing);
            }

            return res;
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


//            this.maxCountBarOnChart = this._recalculateMaxCountBarsOnChart(  );
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
				id = item.id;
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
					id: id,
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

            // if (newMin < this.minPriceOnChart) this.minPriceOnChart = newMin;
            // if (newMax > this.maxPriceOnChart) this.maxPriceOnChart = newMax;
			this.minPriceOnChart = newMin;
            this.maxPriceOnChart = newMax;
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
        repaint: function(){
            this.gr.clearRect( 0, 0, this.width, this.height );
            this.paintArrBars( this.convertBarsToGraphic( this.arrBars, this.firstBarIndent ) );

            this.grRight.clearRect( 0, 0, this.width, this.height );
            this.grBottom.clearRect( 0, 0, this.width, this.height );
            this.paintPrices();
            this.paintTimes();

			this._paintMatchLines();
            if( this.modeDrawForSeek ){
                this.gr.fillStyle = 'rgba(255,255,255,0.9)';
                this.gr.fillRect( 0, 0, this.width, this.height );
				this.repaintRetcsForSeeking();
            }
        },
		/*Узнаем id бара, над которым находится мышка*/
		_knowIdBarHoverMouse: function(mX){
			var self = this;
			var index=false;
			this.arrGrBars.forEach(function(item, key){
				if( mX >= item.x - self.widthBar/2 && mX < item.x + self.widthBar/2 ){
					index = key;
				}
			});
			return index;
		},
		setParamsInGrChart: function(namePair, nameTimeframe){
			// console.log('namePair='+namePair);
			// console.log('nameTimeframe='+nameTimeframe);
			
			var objPair = _.findWhere( manager.arrPairs, {name: this.namePair} );
			if(typeof objPair == 'undefined'){
				writeLog('Индекс валютной пары в массиве не найден. (Функция GrChart.setTimeframe)');
				return false;
			}
			var indexPair = _.indexOf( manager.arrPairs, objPair );
			// console.log('indexPair='+indexPair);
			var objTimeframe = _.findWhere( manager.arrPairs[indexPair].timeFrames, {name: nameTimeframe} );
			if(typeof objTimeframe == 'undefined'){
				writeLog('Таймфрейм не найден. (Функция GrChart.setTimeframe)');
				return false;
			}
			var indexTimeframe = _.indexOf( manager.arrPairs[indexPair].timeFrames, objTimeframe );
			
			
			this.fullArrBars =  manager.arrPairs[indexPair].timeFrames[indexTimeframe].fullChart;
			this.namePair = namePair;
			this.nameTimeframe = nameTimeframe;
			
			this.indexFirstBar = this.fullArrBars.length > +this.maxCountBarOnChart ? this.fullArrBars.length - +this.maxCountBarOnChart : 0;
			this._fillArrBars(  this.indexFirstBar );
			this.arrGrBars = this.convertBarsToGraphic(this.arrBars, this.firstBarIndent);
			this.repaint();
		}

    }



</script>