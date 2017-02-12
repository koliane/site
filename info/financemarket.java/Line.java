package financemarket;

import java.util.ArrayList;

/**
 * @author Tyshkovets Nick
 * @version 1.0
 */
public class Line {
    private static int arrSize = 0;
    private int x1, x2;
    private int y1, y2;
    private int midX, midY;
    
//    private float upDeviation;
//    private float downDeviation;
    private int minCountBar;
    private int maxCountBar;
    private int countPips;
    private int minCountPips;
    private int maxCountPips;
    private int priceSizeInside;
    private int priceSizeOutside;
    private float priceH;
    private float priceL;
    public float numRatio;
    private float deviation;
    
    private boolean absolut;
    private boolean buyLine;
    private boolean sellLine;
    /**Если начало линии и конец максимальные и минимальные по абсолютному значению*/
    private boolean strongLine;
    /**Число пунктов имеет значение?*/
    private boolean importantPips;
    private boolean select;
    
    private Line foreignLine;
    private String foreignName;
    private String name;
    Line(int x1, int y1, int x2, int y2){
        this.x1 = x1;
        this.x2 = x2;
        this.y1 = y1;
        this.y2 = y2;
        name = "Name"+(++arrSize);
        this.maxCountBar = 10;
        this.minCountBar = 10;
        this.deviation = 0;
        importantPips = false;
        
        if( x1 > x2){
            this.x1 = x2;
            this.x2 = x1;
            
            this.y1 = y2;
            this.y2 = y1;
        }
        
        if(this.y1 >= this.y2)
            buyLine = true;
        else 
        if(this.y1 < this.y2)
            sellLine = true;
        calculateMidXY();
    }
    Line(int x1, int y1, int x2, int y2, int countBars){
        this.x1 = x1;
        this.x2 = x2;
        this.y1 = y1;
        this.y2 = y2;
        name = "Name"+(++arrSize);
        this.maxCountBar = countBars;
        this.minCountBar = countBars;
        this.deviation = 0;
        
        if( x1 > x2){
            this.x1 = x2;
            this.x2 = x1;
            
            this.y1 = y2;
            this.y2 = y1;
        }
        
        if(this.y1 >= this.y2)
            buyLine = true;
        else 
        if(this.y1 < this.y2)
            sellLine = true;
        calculateMidXY();
    }
    Line(int x1, int y1, int x2, int y2, int minCountBars, int maxCountBar){
        this.x1 = x1;
        this.x2 = x2;
        this.y1 = y1;
        this.y2 = y2;
        name = "Name"+(++arrSize);
        this.minCountBar = minCountBars;
        this.maxCountBar = maxCountBar;
        this.deviation = 0;
        
        if( x1 > x2){
            this.x1 = x2;
            this.x2 = x1;
            
            this.y1 = y2;
            this.y2 = y1;
        }
        
        if(this.y1 >= this.y2)
            buyLine = true;
        else 
        if(this.y1 < this.y2)
            sellLine = true;
        calculateMidXY();
    }
    Line(int x1, int y1, int x2, int y2, int countBars, boolean strongLine ){
        this.x1 = x1;
        this.x2 = x2;
        this.y1 = y1;
        this.y2 = y2;
        name = "Name"+(++arrSize);
        this.maxCountBar = countBars;
        this.minCountBar = countBars;
        this.strongLine = strongLine;
        this.deviation = 0;
        
        if( x1 > x2){
            this.x1 = x2;
            this.x2 = x1;
            
            this.y1 = y2;
            this.y2 = y1;
        }
        
        if(this.y1 >= this.y2)
            buyLine = true;
        else 
        if(this.y1 < this.y2)
            sellLine = true;
        calculateMidXY();
    }
    Line(int x1, int y1, int x2, int y2, int minCountBars, int maxCountBar, boolean strongLine){
        this.x1 = x1;
        this.x2 = x2;
        this.y1 = y1;
        this.y2 = y2;
        name = "Name"+(++arrSize);
        this.minCountBar = minCountBars;
        this.maxCountBar = maxCountBar;
        this.strongLine = strongLine;
        this.deviation = 0;
        
        if( x1 > x2){
            this.x1 = x2;
            this.x2 = x1;
            
            this.y1 = y2;
            this.y2 = y1;
        }
        
        if(this.y1 >= this.y2)
            buyLine = true;
        else 
        if(this.y1 < this.y2)
            sellLine = true;
        calculateMidXY();
    }

    
    public int getX1(){
        return x1;
    }
    public int getY1(){
        return y1;
    }
    public int getX2(){
        return x2;
    }
    public int getY2(){
        return y2;
    }
    public Line getForeignLine(){
        return this.foreignLine;
    }
    public int getMinCountBar(){
        return this.minCountBar;
    }
    public int getMaxCountBar(){
        return this.maxCountBar;
    }
    public int getCountPips(){
        return this.countPips;
    }
    public float getDeviation(){
        return this.deviation;
    }
    public float getNumRatio(){
        return this.numRatio;
    }
    public static int getArrSize(){
        return arrSize;
    }
    public String getName(){
        return name;
    }
    public int getPriceSizeInside() {
        return priceSizeInside;
    }
    public int getPriceSizeOutside() {
        return priceSizeOutside;
    }    
    public int getMinCountPips() {
        return minCountPips;
    }
    public int getMaxCountPips() {
        return maxCountPips;
    }
    public int getMidX() {
        return midX;
    }
    public int getMidY() {
        return midY;
    }
    public String getForeignName(){
        return foreignName;
    }
    
    public boolean isAbsolut(){
        return absolut;
    }
    public boolean isBuyLine(){
        return buyLine;
    }
    public boolean isSellLine(){
        return sellLine;
    }
    public boolean isStrongLine(){
        return strongLine;
    }
    public boolean isEquals(LineOnChart line){
        
        return true;
    }
    public boolean isImportantPips(){
        return this.importantPips;
    }
    public boolean isSelect(){
        return this.select;
    }
    
    public static void setArrSize(int ind){
        Line.arrSize = ind;
    }
    public void setAbsolut(boolean absolut){
        this.absolut = absolut;
    }
    public void setBuyLine(boolean buyLine){
        this.buyLine = buyLine;
    }
    public void setSellLine(boolean sellLine){
        this.sellLine = sellLine;
    }
    public void setStrongLine(boolean strongLine){
        this.strongLine = strongLine;
    }
    public void setMinCountBars(int countBars){
        this.minCountBar = countBars;
        if(minCountBar > maxCountBar)
            maxCountBar = minCountBar;
    }
    public void setMaxCountBars(int countBars){
        this.maxCountBar = countBars;
        if(maxCountBar < minCountBar)
            minCountBar = maxCountBar;
    }
//    public void setCountPips(int countPips){
//        this.countPips = countPips;
//        this.importantPips = true;
//        calculateMinCountPips();
//        calculateMaxCountPips();
//        
//    }
    public void setDeviation(float deviation){
        this.deviation = deviation;
//        calculateMinCountPips();
//        calculateMaxCountPips();
    }
    public void setMinCountPips( int countPips ){
        this.minCountPips = countPips;
        if( minCountPips > maxCountPips)
            maxCountPips = minCountPips;
//        this.importantPips = true;
    }
    public void setMaxCountPips( int countPips ){
        this.maxCountPips = countPips;
        if( maxCountPips < minCountPips)
            minCountPips = maxCountPips;
//        this.importantPips = true;
    }
    public void setNumRatio(float numRatio){
        this.numRatio = numRatio;
    }
    public void setForeignLine(Line fline){
        this.foreignLine = fline;
        foreignName = fline.getForeignName();
    }
    public void setForeignName(ArrayList<Line> arr, String fname){
        if(fname.equals("")){
            foreignLine = null;
            foreignName = fname;
            this.numRatio = 0f;
            return;
        }
        for(int i=0; i<arr.size(); i++){
            if( arr.get(i).getName().equals(fname) && !name.equals(fname)){
                foreignLine = arr.get(i);
                foreignName = fname;
                break;
            }
        }
    }
    public void setPriceSizeInside(int priceSizeInside) {
        this.priceSizeInside = priceSizeInside;
    }
    public void setPriceSizeOutside(int priceSizeOutside) {
        this.priceSizeOutside = priceSizeOutside;
    }
    public void setImportantPips(boolean importantPips) {
        this.importantPips = importantPips;
    }

    public void setSelect(boolean select) {
        this.select = select;
    }
    public void setName(ArrayList<Line> arr, String name) {
        boolean similar=false;
        for(int i=0;i<arr.size();i++){
            if(arr.get(i).getName().equals(name)){
                similar = true;
                break;
            }
        }
            if(!similar && !name.equals("") && !name.contains(" "))
                this.name = name;
    }
        
    public void calculateCountPips(){
        this.countPips = Math.round( foreignLine.getCountPips() * numRatio );
    }
    public void calculateMinCountPips(){
        this.minCountPips = Math.round( countPips - deviation * countPips );
        if( minCountPips < 0 )
            minCountPips = 0;
    }
    public void calculateMaxCountPips(){
        this.maxCountPips = Math.round( countPips + deviation * countPips );
    }
    public void calculateMidXY(){
        this.midX = Math.round( x1 + Math.abs( x2 - x1 ) /2 );
        if( buyLine )
            this.midY = Math.round( y2 + Math.abs( y2 - y1 ) /2 );
        else
        if( sellLine )
            this.midY = Math.round( y1 + Math.abs( y2 - y1 ) /2 );    
    }
    public void print(){
        System.out.println("x1 = "+x1+"; y1 = "+y1+"; x2 = "+x2+"; y2 = "+y2+"; name = "+name);
    }
}
