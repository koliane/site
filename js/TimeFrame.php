<script>
function TimeFrame(){
    this.name="m1";
    this.pairName = "eurusd";
    // this.visibleWindow = [];
//    this.fullChart = [];
    this.fullChart = [new Bar(2016,7,1,13,0)];
//    this.fullChart = [new Bar(2016,7,1,13,23)];
    this.startId = 0;
    this.endId = 0;
    this.addBarsToChart([2016, 7, 1, 13, 0]);
}


TimeFrame.prototype = {
    constructor: TimeFrame,

    /**Получить необходимые бары с сервера и добавить их в общий график */
    addBarsToChart: function(startTime, endTimeOrCount){
                            var paramObj;
                            var res;
                            if( typeof endTimeOrCount === 'undefined')
                                paramObj = {table: this.pairName, timeFrame: this.name, startTime: startTime }
                            else
                                paramObj = {table: this.pairName, timeFrame: this.name, startTime: startTime, endTime: endTimeOrCount }

                            self=this;

                            $.ajax({
                                url:"/trade/ajax/get_data_from_db.php",
                                data: paramObj,
                                async: false,
                                success: function(jsonData){
                                    if(jsonData == false)
                                        console.warn('Ошибка на стороне сервера. Необходимо проверить входные параметры вызывающейся функии и подключение к базе данных');

                                    try {
                                        var data = JSON.parse(jsonData);

                                        // data = convertToArrObj(data);
                                        dataBars = TimeFrame.prototype.convertToArrObj(data);
                                    }catch(err){
                                        writeLog("Ошибка при парсинге полученных с сервера данных:");
                                        console.warn(err);
                                    }
                                    self._addBarsToChart(dataBars);
//                                    self._addBarsToChart(dataBars);
//                                    console.table(dataBars);
                                    console.table(self.fullChart);
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

                            /**Если последний элемент массива fullChart равен первому элементу массива arrBars, то присоединяем arrBars к концу fullChart, иначе - к началу*/
                            if( this.fullChart.length === 0 || this.fullChart[ this.fullChart.length - 1 ].equalsTime( arrBars[0]) === 0){
//                                console.table( arrBars.map( function( item ){
//                                this.fullChart = this.fullChart.concat( arrBars.map( function( item ){
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
                                alert();
                                this.fullChart =  (arrBars.slice(0,-1)).map( function( item ){
//                                this.fullChart =  arrBars.map( function( item ){
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

//                            if( this.fullChart.length === 0 ){
//                                this.fullChart = arrBars.map( function( item ){
//                                    item.id = self._generateNextId();
//                                    /**Преобразуем строки к числовому типу*/
//                                    for(var key in item) {
////                                        if( item.hasOwnProperty( key ) )
//                                        if( key=="year" || key=="month" || key=="day" || key=="hour" || key=="minute" || key=="open" ||
//                                            key=="high" || key=="low" || key=="close" || key=="volume"  )
//                                            item[ key ] = +item[ key ];
//                                    }
//                                    /**----------------------*/
//                                    return item;
//                                });
//                            }
                        },
    _generateNextId:  function(){
                            return ++this.endId;
                        },
    _generatePrevId:  function(){
                            return --this.startId;
                        },

}
</script>