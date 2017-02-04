function Bar(year, month, day, hour, minute, open, high, low, close, volume){
    this.year = year;
    this.month = month;
    this.day = day;
    this.hour = hour;
    this.minute = minute;
    this.open = open;
    this.high = high;
    this.low = low;
    this.close = close;
    this.volume = volume;
    this.id = '';
};
Bar.prototype = {
    constructor: Bar,
    equalsTime: function( arrTime , fieldsForEquals){
        var defautFieldsForEquals = 'year,month,day,hour,minute';
        fieldsForEquals = fieldsForEquals || defautFieldsForEquals;

        if( !(arrTime instanceof Array) || typeof fieldsForEquals !=='string' ){
            writeLog('Не верный формат входных параметров');
            return -10;
        }

        fieldsForEquals = fieldsForEquals.replace(/\s/g, '');
        fieldsForEquals = fieldsForEquals.toLowerCase();

        var arrDefautFieldsForEquals = defautFieldsForEquals.split(',');
        var fieldsForEquals = fieldsForEquals.split(',');

        // fieldsForEquals = fieldsForEquals.replace(/[^{year}]+/g, '');

        alert(fieldsForEquals);
    }
}

function Pair(){
    this.name="";
    this.timeFrames=[];
}

function TimeFrame(){
    this.name="h";
    this.visibleWindow = [];
    this.fullChart = [];
    this.negativeCurrentId = 0;
    this.positiveCurrentId = 0;
}

var allCharts = {
}
allCharts['eurusd'] = new Pair();

// Bar.prototype = {
//     constructor: Bar,
//     parseVar: function( str ){
//         if(typeof str == 'number')
//             return str;
//         console.log(typeof str);
//         if( str.indexOf( '.' ) === -1 )
//             return parseInt( str );
//         else
//             return parseFloat( str );
//     }
// }

var visibleWindow;
var fullChart = [];
var tf = new TimeFrame();
// tf.equals('hello')
var br = new Bar(2014,09,5,21,42);
br.equalsTime([2014,09,5,21,42])

$.ajax({
    url:"/trade/ajax/get_data_from_db.php",
    data:{table:'eurusd', timeFrame:'m1', startTime:[2016, 7, 1, 13, 0]},
    success: function(jsonData){
        if(jsonData == false)
            console.warn('Ошибка на стороне сервера. Необходимо проверить входные параметры вызывающейся функии и подключение к базе данных');

        try {
            var data = JSON.parse(jsonData);

            data = convertToArrObj(data);
            addToFullChart(data, tf);
        }catch(err){
            writeLog("Ошибка при парсинге полученных с сервера данных:");
            console.warn(err);
        }
        // writeLog();
        // console.log(data);
        // data = convertToArrObj(data);
        // addToFullChart(data, tf);
        // console.table(data);
        // console.table(tf.fullChart);
    }
});

/**Преобразовать данные, полученные с сервера, в формат, пригодный для добавления в результирующий массив котировок (fullChart)*/
function convertToArrObj( data ){
    var arrRes = [];
    data.forEach(function(item){
        arrRes.push(new Bar( item[0], item[1], item[2], item[3], item[4], item[5], item[6], item[7], item[8], item[9]));
    });
    return arrRes;
}

function addToFullChart( arrData , timeframe ){
    if( timeframe.fullChart.length === 0 ) {
        arrData = arrData.map( function(item){
            item.id = ++timeframe.positiveCurrentId;
            timeframe.fullChart.push(item);
            return item;
        });
        return true;
    }
    // var firstBarInChart = [ fullChart[0].year, fullChart[0].month, fullChart[0].day, fullChart[0].hour, fullChart[0].minute ];
    // var lastIndex = fullChart.length-1;
    // var endBarInChart = [ fullChart[lastIndex].year, fullChart[lastIndex].month, fullChart[lastIndex].day, fullChart[lastIndex].hour, fullChart[lastIndex].minute ]


}

/**Если входной параметр - строка, то привести ее к числовому формату*/
function parseVar( str ){
    if(typeof str == 'number')
        return str;
    if(typeof str == 'string') {
        if (str.indexOf('.') === -1)
            return parseInt(str);
        else
            return parseFloat(str);
    }
    return false;
}


