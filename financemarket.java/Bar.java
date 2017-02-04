package financemarket;
import java.awt.*;

/**
 *  Класс описания бара
 * @author Tyshkovets Nick
 * @version 1.0
 */
public class Bar {
    private float priceO, priceH, priceL, priceC;
    /**Поле - объем*/
    private int volume;
    private TheDate date;
    private Color color;
    
    
    Bar(){};
    
    Bar(Bar bar) {
        this.priceO = bar.getPriceO();
        this.priceH = bar.getPriceH();
        this.priceL = bar.getPriceL();
        this.priceC = bar.getPriceC();
        this.volume = bar.getVolume();
        this.date = bar.getDate();
    }
    
    Bar(TheDate date, float priceO, float priceH, float priceL, float priceC) {
        this.priceO=priceO;
        this.priceH=priceH;
        this.priceL=priceL;
        this.priceC=priceC;
        date = new TheDate();
    }
    
    Bar(float priceO, float priceH, float priceL, float priceC, int volume) {
        this.priceO = priceO;
        this.priceH = priceH;
        this.priceL = priceL;
        this.priceC = priceC;
        this.volume = volume;
    }
    
    /**Конструктор преобразует строку в параметры Бара*/
    Bar(String dataInLine) {
        date = new TheDate();
        String[] subString = dataInLine.split(",");
        
        try{
            String[] stringDate = subString[0].split("\\.");
            this.date.setYear(Integer.parseInt(stringDate[0]));
            this.date.setMonth(Integer.parseInt(stringDate[1]));
            this.date.setDay(Integer.parseInt(stringDate[2]));

            String[] stringTime = subString[1].split(":");
            this.date.setHour(Integer.parseInt(stringTime[0]));
            this.date.setMinute(Integer.parseInt(stringTime[1]));

            this.priceO = Float.parseFloat( subString[2] );
            this.priceH = Float.parseFloat( subString[3] );
            this.priceL = Float.parseFloat( subString[4] );
            this.priceC = Float.parseFloat( subString[5] );
            this.volume = Integer.parseInt( subString[6] );
        }catch(NumberFormatException nfe){
            System.out.println(""+nfe+"\nИсключение в конструкторе Bar -> Bar() -> Integer.parseInt(String) | Float.parseFloat(String): строка не эквивалентна заданному численному типу");
        }catch(ArrayIndexOutOfBoundsException aie){
            System.out.println(""+aie+"\nИсключение в конструкторе Bar -> Bar() -> subString[i]: обращение к несуществующему индексу массива");
        }catch(Exception ex){
            System.out.println(""+ex+"\nИсключение в конструкторе Bar -> Bar()");
        }
    }
    
    /**
     * Получить цену открытия
     * @return Возвращает цену открытия
     */
    public float getPriceO(){
        return this.priceO;
    }
    public float getPriceH(){
        return this.priceH;
    }
    public float getPriceL(){
        return this.priceL;
    }
    public float getPriceC(){
        return this.priceC;
    }
    public int getVolume(){
        return this.volume;
    }    
    
    /**
     * 
     * @param priceO Задает цену открытия
     */
    public void setPriceO( float priceO ) {
        this.priceO = priceO;
    }
    public void setPriceH( float priceO ) {
        this.priceH = priceH;
    }
    public void setPriceL( float priceO ) {
        this.priceL = priceL;
    }
    public void setPriceC( float priceO ) {
        this.priceC = priceC;
    }
    public void setVolume( int volume ) {
        this.volume = volume;
    }   
    
    public TheDate getDate(){
        return this.date;
    }
    
    public void print() {
        System.out.println(""+date.getYear()+"-" + date.getMonth()+"-" + date.getDay() + " " + date.getHour() + ":" + date.getMinute()
                                    + ", " + priceO + ", " + priceH + ", " + priceL + ", " + priceC + ", " + volume);
    }
}
