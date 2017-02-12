<script>
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


                    if( arrTime instanceof Bar ){
                        var arrOfBarFields = [ arrTime.year, arrTime.month, arrTime.day, arrTime.hour, arrTime.minute ];
                        arrTime = arrOfBarFields;
                    }
 
                    if( !(arrTime instanceof Array) || typeof fieldsForEquals !=='string' || arrTime.length === 0 ){
                        writeLog('Не верный формат входных параметров');
                        return false;
                    }

                    fieldsForEquals = fieldsForEquals.replace(/\s/g, '');
                    fieldsForEquals = fieldsForEquals.toLowerCase();

                    var arrFieldsForEquals = fieldsForEquals.split(',');
                    if( arrTime.length < arrFieldsForEquals.length ){
                        writeLog('Количество полей массива меньше количества полей для сравнения (второй параметр ф-ии)');
                        return false;
                    }

                    /**Если попадается в массиве arrTime число, значение которого меньше аналогичного значения в объекте, то возвращаем 1, если больше - -1. Если все значения совпали, возвращаем 0*/
                    for( var i = 0; i < arrTime.length; i++){
                        if( arrTime[ i ] > this[ arrFieldsForEquals[i] ])
                            return -1;
                        else if( arrTime[ i ] < this[ arrFieldsForEquals[i] ])
                            return 1;

                        if( i === arrTime.length - 1 )
                            return 0;
                    }
                }
}
</script>