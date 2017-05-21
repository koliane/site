<script>
    function WindowsManager() {
        var self = this;
        //alert(self.defaultWidth)
        this.defaultWidthWindow = 1000;
        this.defaultHeightWindow = 350;
        this.defaultTopIndent = 40;
        this.defaultBottomIndent = 40;
        this.defaultRightIndent = 100;
//        this.defaultLeftIndent = 0;
        this.defaultWidthPriceIndicator = 80;
        this.defaultHeightTimeIndicator = 80;

        this.defaultWidthBar = 8;
        this.defaultBarSpacing = 5;
        this.defaultScale = 5;

        this.defaultBuyBarColor = '#00E100';
        this.defaultSellBarColor = '#FF0000';
        this.defaultBodyBorderBarColor = '#9B9B9B';
        this.defaultTopShadowBarColor = '#9B9B9B';
        this.defaultBottomShadowBarColor = '#9B9B9B';

        this.defaultTextColor = '#949494';
        this.defaultTextSize = 12;
        this.defaultTextFont = "" + this.defaultTextSize + "px sans-serif";
//        this.defaultTextFont = "bold " + this.defaultTextSize + "px sans-serif";
        /**Коэффициент минимальной разряженности между отображенными ценами (мин. расстояние вычисляется следующим образом: РазмерШрифта * Коэффициент разряженности)*/
        this.defaultRatioPriceSpacing = 3;

        /**Минимальное расстояние между началом каждого времени (для отображения на графике)*/
        this.defaultTimeSpacing = 150;

        this.arrPairs = [];
        this.arrWindow = [];
        this.lastChartIndex = 0;
        this.arrPairs.push(new Pair('eurusd', 'm1', this.arrPairs.length, {countBarsForGet: 5, decimalPlaces: 4}));


        this.arrWindow.push(this.createNewWindow(0));
//        console.log(this.arrPairs[0].timeFrames[0].fullChart);


    }
    WindowsManager.prototype = {
        constructor: WindowsManager,
        /**Метод создает окно с графиком и добавляет его в arrWindow
         *
         * @param width - ширина окна(включая поля для вывода времени и цен)
         * @param height - длина окна
         * @param posPriceFields - позиции поля для вывода цен (0 - не показывать поле, 1 - поле слева от графика, 2 - поле справа от графика, 3 - поле с двух сторон)
         * @param posTimeFields - позиции поля для вывода времени и дат (0 - не показывать поле, 1 - поле сверху  графика, 2 - поле снизу графика, 3 - поле сверху и снизу)
         * @param settingsPriceFields - объект, в котором задаются настройки для полей "цен". Свойства leftWidth, rightWidth (ширина левого и правого полей)
         * @param settingsTimeFields - объект, в котором задаются настройки для полей "времени". Свойства topHeight, bottomHeight
         */
        createNewWindow: function (pairId, width, height, posPriceFields, posTimeFields, settingsPriceFields, settingsTimeFields) {
            ++this.lastChartIndex;
            /**Если параметры не заданы, то используем параметры по умолчанию*/
            width = width || this.defaultWidthWindow;
            height = height || this.defaultHeightWindow;
            if (posPriceFields === 0 || posPriceFields > 3 || posPriceFields < 0)
                posPriceFields = 0;
            else
                posPriceFields = posPriceFields || 2;

            if (posTimeFields === 0 || posTimeFields > 3 || posTimeFields < 0)
                posTimeFields = 0;
            else
                posTimeFields = posTimeFields || 2;

            /**Если ширина поля цен не указана в параметре "settingsPriceFields", то устанавливаем ширину по умолчанию*/
            if (!(  settingsPriceFields && typeof settingsPriceFields['leftWidth'] != 'undefined' && typeof
                    settingsPriceFields['rightWidth'] != 'undefined')) {
                if (typeof settingsPriceFields == 'undefined')
                    settingsPriceFields = {};

                if ((posPriceFields == 1 || posPriceFields == 3) && typeof settingsPriceFields['leftWidth'] == 'undefined')
                    settingsPriceFields['leftWidth'] = this.defaultWidthPriceIndicator;
                if ((posPriceFields == 2 || posPriceFields == 3) && typeof settingsPriceFields['rightWidth'] == 'undefined')
                    settingsPriceFields['rightWidth'] = this.defaultWidthPriceIndicator;

                if (posPriceFields == 0) {
                    settingsPriceFields['leftWidth'] = 0;
                    settingsPriceFields['rightWidth'] = 0;
                }
            }
            /**Если высота поля времени не указана в параметре "settingsTimeFields", то устанавливаем ширину по умолчанию*/
            if (!(settingsTimeFields && typeof settingsTimeFields['topHeight'] != 'undefined' && typeof
                    settingsTimeFields['bottomHeight'] != 'undefined')) {

                if (typeof settingsTimeFields == 'undefined')
                    settingsTimeFields = {};

                if ((posTimeFields == 1 || posTimeFields == 3) && typeof settingsTimeFields['topHeight'] == 'undefined')
                    settingsTimeFields['topHeight'] = this.defaultHeightTimeIndicator;
                if ((posTimeFields == 2 || posTimeFields == 3) && typeof settingsTimeFields['bottomHeight'] == 'undefined')
                    settingsTimeFields['bottomHeight'] = this.defaultHeightTimeIndicator;

                if (posTimeFields == 0) {
                    settingsTimeFields['topHeight'] = 0;
                    settingsTimeFields['bottomHeight'] = 0;
                }
            }

            /**Вычисляем размеры canvas для отображения графика*/
            var mainCanvasWidth, mainCanvasHeight;
            switch (posPriceFields) {
                case 0:
                    mainCanvasWidth = width;
                    break;
                case 1:
                    mainCanvasWidth = width - settingsPriceFields['leftWidth'];
                    break;
                case 2:
                    mainCanvasWidth = width - settingsPriceFields['rightWidth'];
                    break;
                case 3:
                    mainCanvasWidth = width - settingsPriceFields['leftWidth'] - settingsPriceFields['rightWidth'];
                    break;
            }
            switch (posTimeFields) {
                case 0:
                    mainCanvasHeight = height;
                    break;
                case 1:
                    mainCanvasHeight = height - settingsTimeFields['topHeight'];
                    break;
                case 2:
                    mainCanvasHeight = height - settingsTimeFields['bottomHeight'];
                    break;
                case 3:
                    mainCanvasHeight = height - settingsTimeFields['topHeight'] - settingsTimeFields['bottomHeight'];
                    break;
            }

            /**Определяем местоположения каждого canvas*/
            var strStyleMain = strStyleTop = strStyleBottom = strStyleLeft = strStyleRight = '';

            if (posPriceFields == 1) {
                strStyleMain = 'left:' + settingsPriceFields['leftWidth'] + 'px;';
                strStyleTop = 'left:' + settingsPriceFields['leftWidth'] + 'px;';
                strStyleBottom = 'left:' + settingsPriceFields['leftWidth'] + 'px;';
            } else if (posPriceFields == 2) {
                strStyleRight = 'left:' + mainCanvasWidth + 'px;';
            } else if (posPriceFields == 3) {
                strStyleMain = 'left:' + settingsPriceFields['leftWidth'] + 'px;';
                strStyleTop = 'left:' + settingsPriceFields['leftWidth'] + 'px;';
                strStyleBottom = 'left:' + settingsPriceFields['leftWidth'] + 'px;';
                strStyleRight = 'left:' + +(settingsPriceFields['leftWidth'] + mainCanvasWidth) + 'px;';
            }

            if (posTimeFields == 1) {
                strStyleMain = strStyleMain + 'top:' + settingsTimeFields['topHeight'] + 'px';
                strStyleLeft = strStyleLeft + 'top:' + settingsTimeFields['topHeight'] + 'px';
                strStyleRight = strStyleRight + 'top:' + settingsTimeFields['topHeight'] + 'px';
            } else if (posTimeFields == 2) {
                strStyleBottom = strStyleBottom + 'top:' + mainCanvasHeight + 'px';
            } else if (posTimeFields == 3) {
                strStyleMain = strStyleMain + 'top:' + settingsTimeFields['topHeight'] + 'px';
                strStyleLeft = strStyleLeft + 'top:' + settingsTimeFields['topHeight'] + 'px';
                strStyleRight = strStyleRight + 'top:' + settingsTimeFields['topHeight'] + 'px';
                strStyleBottom = strStyleBottom + 'top:' + +(settingsTimeFields['topHeight'] + mainCanvasHeight) + 'px';
            }

            /**Составляем по частям итоговую строку html*/
            var strMainCanv = strTopCanv = strBottomCanv = strLeftCanv = strRightCanv = '';
            strMainCanv = '<canvas id="canvas' + (this.lastChartIndex) + '" class="main" width="' + mainCanvasWidth + '" height="' + mainCanvasHeight + '" style="' + strStyleMain + '"></canvas>';
            if (posTimeFields == 1 || posTimeFields == 3)
                strTopCanv = '<canvas id="topCanvas' + (this.lastChartIndex) + '" class="topCanvas" width="' + mainCanvasWidth + '" height="' + settingsTimeFields['topHeight'] + '" style="' + strStyleTop + '"></canvas>';

            if (posTimeFields == 2 || posTimeFields == 3)
                strBottomCanv = '<canvas id="bottomCanvas' + (this.lastChartIndex) + '" class="bottomCanvas" width="' + mainCanvasWidth + '" height="' + settingsTimeFields['bottomHeight'] + '" style="' + strStyleBottom + '"></canvas>';

            if (posPriceFields == 1 || posPriceFields == 3)
                strLeftCanv = '<canvas id="leftCanvas' + (this.lastChartIndex) + '" class="leftCanvas" width="' + settingsPriceFields['leftWidth'] + '" height="' + mainCanvasHeight + '" style="' + strStyleLeft + '"></canvas>';

            if (posPriceFields == 2 || posPriceFields == 3)
                strRightCanv = '<canvas id="rightCanvas' + (this.lastChartIndex) + '" class="rightCanvas" width="' + settingsPriceFields['rightWidth'] + '" height="' + mainCanvasHeight + '" style="' + strStyleRight + '"></canvas>';

//            var windowMenu = '<div id="window_menu_item_' + (this.lastChartIndex) + '" class="window_menu_item"><i class="fa fa-pencil-square" aria-hidden="true"></i></div>';
            var windowMenu = '';

            var strHtml = '<div id="window' + (this.lastChartIndex) + '" class="window">' + windowMenu + '<div class="window-chart">'
                + strMainCanv + strTopCanv + strBottomCanv + strLeftCanv + strRightCanv + '</div></div>';
//            var strHtml = '<div id="window' + (this.lastChartIndex) + '" class="window">' + windowMenu
//                + strMainCanv + strTopCanv + strBottomCanv + strLeftCanv + strRightCanv + '</div>';
            $('.windows').append(strHtml);

            this.arrWindow.push(new GrChart(mainCanvasWidth, mainCanvasHeight,
                {
                    settingsPriceFields: settingsPriceFields,
                    settingsTimeFields: settingsTimeFields,

                    topIndent: this.defaultTopIndent,
//                                                leftIndent: this.defaultLeftIndent,
                    rightIndent: this.defaultRightIndent,
                    bottomIndent: this.defaultBottomIndent,

                    barSpacing: this.defaultBarSpacing,
                    widthBar: this.defaultWidthBar,
                    lastChartIndex: this.lastChartIndex,

                    buyBarColor: this.defaultBuyBarColor,
                    sellBarColor: this.defaultSellBarColor,
                    bodyBorderBarColor: this.defaultBodyBorderBarColor,
                    topShadowBarColor: this.defaultTopShadowBarColor,
                    bottomShadowBarColor: this.defaultBottomShadowBarColor,

                    textColor: this.defaultTextColor,
                    textFont: this.defaultTextFont,
                    textSize: this.defaultTextSize,
                    ratioPriceSpacing: this.defaultRatioPriceSpacing,

                    timeSpacing: this.defaultTimeSpacing,

                    decimalPlaces: this.arrPairs[pairId].decimalPlaces,
                }, this)
            );
            return this.lastChartIndex;
        },
        paint: function () {

        }
    }

</script>