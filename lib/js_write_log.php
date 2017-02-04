<script>
writeLog = function(errorStr) {
            <?
            /**Корректировка $phpLogLevel, если указана не верно*/
            if( !isset($jsLogLevel) )
                $jsLogLevel = 0;

            if( $jsLogLevel < 0)
                $jsLogLevel = 0;
            elseif($jsLogLevel > 9)
                $jsLogLevel = 9;

            if( !($jsLogLevel >= 0 && $jsLogLevel <= 9) )
                $jsLogLevel = 0;
            /********************/

            if( $jsLogLevel ){

                ?>
                if(typeof errorStr === 'undefined' )
                errorStr = "";
                var d=new Date();
                function to2digit( digit ){
                if(digit<10)
                return '0'+digit;
                else
                return digit;
                }
                var strDate = ''+d.getFullYear()+'-'+to2digit( +(d.getMonth()+1) ) + '-' + to2digit( d.getDay() ) + ' ' + to2digit( d.getHours() ) + ':' + to2digit( d.getMinutes() ) + ':' + to2digit( d.getSeconds() );

                function getError()
                {
                try { throw new Error() }
                catch(err) { return err }
                }

                try {
                var errorStack = getError().stack;

                var arrMatches = errorStack.match(/.+/ig);
                if (arrMatches[0] === 'Error') {
                arrMatches.shift();
                }
                var str = arrMatches[2];
                str = str.replace(/^\s*/, '').replace(/\s*$/, '');

                var endDigit = str.match(/(:\d*\W*)$/)[0];
                str = str.slice(0, str.lastIndexOf(endDigit));

                str = str.replace(/^(\S*\s+)*\W*/, '');

                var resStr = '_JS  ' + strDate + ' Error __' + errorStr + '__ in ' + str;
                <?if( $jsLogLevel > 2 ) {?>
                    console.warn(resStr);
                    console.warn(errorStack);
                <?}?>
                resStr = resStr + ';  browser = ' + navigator.userAgent + ';';

                $.ajax({
                url: "/trade/ajax/write_js_log.php",
                data: {strError: resStr, errorStack: errorStack}
                });
                }catch(e){
                console.warn('Ошибка при определении строки вызова функции. Внимание! Скорее всего браузер не поддерживает getError().stack');
                console.warn(e);
                }
            <?}?>
}
</script>