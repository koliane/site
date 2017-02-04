package financemarket;
import java.io.*;
import java.util.*;
/**
 * @author Tyshkovets Nick
 */
public class Chart {
    private ArrayList <Bar> arrBars;
    private Bar startBar;
    private Bar endBar;
    /**@param Количество баров в файле*/
    private int totalBar;


    Chart() {
        arrBars = new ArrayList<Bar>();
    }
    
    public ArrayList<Bar> getArrBars() {
        return this.arrBars;
    }
    
    Chart(String pathPrices) throws FileNotFoundException, IOException {
        arrBars = new ArrayList<Bar>();
        readChart(pathPrices);
    }    
    
    /**
     * 
     * @param pathPrices Путь к файлу с котировками
     * @param barOnList Количество Бар, загружаемых в массив
     */
    Chart(String pathPrices, int barOnList) {
        arrBars = new ArrayList<Bar>();
        
        try{
            BufferedReader reader = new BufferedReader(new FileReader( pathPrices ));
            String line;
            String tmpStart=null;
            
            if( (line = reader.readLine()) != null ) {
                this.totalBar = 1;
                this.endBar = new Bar(line);
                arrBars.add(endBar);
                
                while ( (line = reader.readLine() ) != null ) {
                    tmpStart = line;
                    
                    if( totalBar-1 < barOnList ){
                        arrBars.add(new Bar(line));
                    }  
                    totalBar++;
                }
                
                if(tmpStart == null) {
                    this.startBar = new Bar(this.endBar);
                    arrBars.add(this.startBar);
                }
                else
                    this.startBar = new Bar(tmpStart);
                
                reader.close();
            }
            else
                totalBar=0;
            reader.close();
        }catch(IOException ioe) {
            System.out.println("" + ioe +"\nИсключение IOException в конструкторе класса FieldMarket(String)");
        }
    }
    
    public int getTotalBar(){
        return totalBar;
    }
    
    public String readLine(RandomAccessFile r) throws IOException{
        if(r.getFilePointer()==0)
            return null;
        String str = new String();
        byte symb=0;
        
        while( r.getFilePointer() >= 0 && (symb=r.readByte()) != '\n'){
            str = str + (char)symb;
            if(r.getFilePointer()-2>=0)
                r.seek(r.getFilePointer()-2);
            else {
                r.seek(0);
                break;
            }
        }
        
        StringBuilder strB = new StringBuilder(str);
        strB.reverse();
        str = strB.toString();
        if(symb=='\n'){
            r.seek(r.getFilePointer()-3);
        }
        return str;
    }
    void readPrices(String pathPrices) {
        try{
            BufferedReader reader = new BufferedReader(new FileReader( pathPrices ));
            String line;

            while ( (line = reader.readLine() ) != null ) 
                arrBars.add(new Bar(line));
            reader.close();
        }catch(IOException ioe) {
            System.out.println("" + ioe +"\nИсключение IOException в объекте класса FieldMarket -> readPrices(String)");
        }
    }
    
    public void readChart(String pricePath){
        arrBars.clear();
        try{
            RandomAccessFile reader = new RandomAccessFile(pricePath,"r");
            reader.seek(reader.length()-1);
            String line;
            
            if( (line = readLine(reader)) != null ) {
                arrBars.add(0, new Bar(line));
                
                while ( (line = readLine(reader) ) != null ) 
                    arrBars.add(0, new Bar(line));
                reader.close();
            }
            else
                totalBar=0;
            reader.close();
        }catch(IOException ioe) {
            System.out.println("" + ioe +"\nИсключение IOException в конструкторе класса Chart(String):"
                                        +" ошибка ввода-вывода; вероятнее всего файла по данному пути не существует");
        }catch(Exception e) {
            System.out.println("" + e +"\nИсключение Exception в конструкторе класса Chart(String):");
        }
        this.totalBar = arrBars.size();
    }
    
    public void printArr() {
        System.out.println("Распечатка загруженных данных...");        
        for (int i=0;i<this.arrBars.size();i++) {
            System.out.print(""+(int)(i+1)+") ");
            this.arrBars.get(i).print();
        }
    }
    
    
}
