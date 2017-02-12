package financemarket;
import static java.lang.Math.abs;
import java.util.*;

/**
 *
 * @author Николай
 */
public class TheDate {
    private int year, month, day, hour, minute;
    
    TheDate() {}
    TheDate(int theYear, int theMonth, int theDay, int theHour, int theMinute){
        this.year = theYear;
        this.month = theMonth;
        this.day = theDay;
        this.hour = theHour;
        this.minute = theMinute;
    }
    
    public int getYear() {
        return this.year;
    }
    public int getMonth() {
        return this.month;
    }
    public int getDay() {
        return this.day;
    }
    public int getHour() {
        return this.hour;
    }
    public int getMinute() {
        return this.minute;
    }
 
    public void setYear( int theYear ) {
        this.year = theYear;
    }
    public void setMonth( int theMonth ) {
        this.month = theMonth;
    }
    public void setDay( int theDay ) {
        this.day = theDay;
    }
    public void setHour( int theHour ) {
        this.hour = theHour;
    }
    public void setMinute( int theMinute ) {
        this.minute = theMinute;
    }
    
    public static int getMinuteDistance(TheDate date1, TheDate date2) {
        return abs( date2.getYear()-date1.getYear() )+abs( date2.getMonth()-date1.getMonth() )+abs( date2.getDay()-date1.getDay() 
                            + abs( date2.getHour()-date1.getHour() ) + abs( date2.getMinute()-date1.getMinute() ));
    }
    
    public boolean isEquals(TheDate date){
        if( this.year == date.getYear() && this.month == date.getMonth() && this.day == date.getDay()
                    && this.hour == date.getHour() && this.minute == date.getMinute() )
            return true;
        else
            return false;
    }
    
    public String convertMonthToString(){
        String str = new String();
        switch(this.month){
            case 1: str="Jan";break;
            case 2: str="Feb";break;
            case 3: str="Mar";break;
            case 4: str="Apr";break;
            case 5: str="May";break;
            case 6: str="Jun";break;
            case 7: str="Jul";break;
            case 8: str="Aug";break;
            case 9: str="Sep";break;
            case 10: str="Oct";break;
            case 11: str="Nov";break;
            case 12: str="Dec";
        }
        return str;
    }
}
