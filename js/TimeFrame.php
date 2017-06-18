<script>
function TimeFrame(pairName, name, managerSettings){
    this.pairName = pairName;
    this.name = name;
    // this.visibleWindow = [];
//    this.fullChart = [];
    this.fullChart = [];
    this.startId = 0;
    this.endId = 0;
    this.addBarsToChart("", managerSettings.countBarsForGet);
	this.fullMatches = [];
}


TimeFrame.prototype = {
    constructor: TimeFrame,

    /**Получить необходимые бары с сервера и добавить их в общий график */
    addBarsToChart: function(startTime, endTimeOrCount, funcSuccess){
                            var paramObj;
                            var res;

                            if( typeof endTimeOrCount === 'undefined' && typeof startTime === 'undefined'){
                                writeLog('Входные параметры отсутствуют или не определены');
                                return false;
                            }

                            /**Определяем какие*/
                            if( typeof endTimeOrCount === 'undefined')
                                paramObj = {table: this.pairName, timeFrame: this.name, startTime: startTime }
                            else
                                paramObj = {table: this.pairName, timeFrame: this.name, startTime: startTime, endTime: endTimeOrCount }

                            var self=this;

                            $.ajax({
                                url:"/trade/ajax/get_data_from_db.php",
                                data: paramObj,
                                async: false,
                                success: function(jsonData){
                                    if(jsonData == false)
                                        console.warn('Ошибка на стороне сервера. Необходимо проверить входные параметры вызывающейся функии и подключение к базе данных');
//                                    console.log(jsonData);
                                    try {
                                        var data = JSON.parse(jsonData);

                                        // data = convertToArrObj(data);
                                        dataBars = TimeFrame.prototype.convertToArrObj(data);
                                    }catch(err){
                                        writeLog("Ошибка при парсинге полученных с сервера данных:");
                                        console.warn(err);
                                    }
                                    self._addBarsToChart(dataBars);
									
									if( typeof funcSuccess != 'undefined' ){
										funcSuccess();
									}
//                                    self._addBarsToChart(dataBars);
//                                    console.table(dataBars);
                                    // console.table(self.fullChart);
                                }
                            });
                        },
    /**Преобразовать данные, полученные с сервера, в формат, пригодный для добавления в результирующий массив котировок (fullChart)*/
    convertToArrObj: function( data ){
                        var arrRes = [];
                        data.forEach(function(item){
                            arrRes.push(new Bar( item[0], item[1], item[2], item[3], item[4], item[5], item[6], item[7], item[8], item[9]));
                        });
                        return arrRes;
                    },
    /**Добавить бары в общий график в правильной последовательности*/
    _addBarsToChart: function( arrBars ) {
                            self = this;

                            if( !arrBars || !(arrBars instanceof Array)){
                                writeLog('Входной параметр функции либо пуст, либо не является массивом');
                                return false;
                            }
                            /**Если массив fullChart пустой, то добавляем все полученные данные*/
                            if(this.fullChart.length === 0){
                                this.fullChart = arrBars.map( function( item ){
                                    item.id = self._generateNextId();
                                    /**Преобразуем строки к числовому типу*/
                                    for(var key in item) {
                                        if( key=="year" || key=="month" || key=="day" || key=="hour" || key=="minute" || key=="open" ||
                                            key=="high" || key=="low" || key=="close" || key=="volume"  )
                                            item[ key ] = +item[ key ];
                                    }
                                    return item;
                                }) ;
                            }else
                            /**Если последний элемент массива fullChart равен первому элементу массива arrBars, то присоединяем arrBars к концу fullChart, иначе - к началу*/
                            if(  this.fullChart[ this.fullChart.length - 1 ].equalsTime( arrBars[0]) === 0){
                                this.fullChart = this.fullChart.concat( (arrBars.slice(1)).map( function( item ){
                                    item.id = self._generateNextId();
                                    /**Преобразуем строки к числовому типу*/
                                    for(var key in item) {
                                        if( key=="year" || key=="month" || key=="day" || key=="hour" || key=="minute" || key=="open" ||
                                            key=="high" || key=="low" || key=="close" || key=="volume"  )
                                            item[ key ] = +item[ key ];
                                    }
                                    /**----------------------*/
                                    return item;
                                }) );
                            } else {
                                this.fullChart =  (arrBars.slice(0,-1)).map( function( item ){
                                    item.id = self._generatePrevId();
                                    /**Преобразуем строки к числовому типу*/
                                    for(var key in item) {
                                        if( key=="year" || key=="month" || key=="day" || key=="hour" || key=="minute" || key=="open" ||
                                            key=="high" || key=="low" || key=="close" || key=="volume"  )
                                            item[ key ] = +item[ key ];
                                    }
                                    /**----------------------*/
                                    return item;
                                }).concat(this.fullChart);
                            }

                        },
    _generateNextId:  function(){
                            return ++this.endId;
                        },
    _generatePrevId:  function(){
                            return --this.startId;
                        },

}
</script>