package financemarket;

import javax.swing.*;
import java.awt.*;
import java.awt.event.ComponentEvent;
import java.awt.event.ComponentListener;
import java.awt.event.KeyEvent;
import java.awt.event.KeyListener;
import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;
import java.awt.event.MouseMotionListener;
import java.awt.event.MouseWheelEvent;
import java.awt.event.MouseWheelListener;
import java.io.IOException;
import java.io.RandomAccessFile;
import java.lang.*;

import static java.lang.Math.*;
import java.math.BigDecimal;
import java.math.RoundingMode;
import java.util.ArrayList;
import java.util.concurrent.TimeUnit;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 * @author Николай
 */
public class ChartPanel extends JPanel
        implements MouseListener, MouseMotionListener, MouseWheelListener, KeyListener{
// <editor-fold defaultstate="collapsed" desc="Variable Declaration">   
    /**
     * Ширина Бара, и шаг пункта по вертикали
     */
    private int widthBar, dyBar;
    /**
     * Расстояние между барами
     */
    private int ddx;
    /**
     * Отступ сверху и снизу рабочего окна, для более удобного отображения графика
     */
    private int yIndent;
    /**
     * Отступ справа
     */
    private int rightIndent;
    /**
     * Количество Баров отображаемых на поле (на панели)
     */
    private int limitBarsOnPanel;
    /**
     * Множитель показывает во сколько раз больше необходимо загружать баров в
     * массив по сравнению с числом баров на видимом поле
     */
    private int mnojitel;
    /**
     * Индекс Бара из массива в памяти, который первым отображается на панели.
     * startArrIndex - левый элемент
     */
    private int startArrIndex, endArrIndex;
    /**
     * Передвижение графика при заданном перемещении мыши(зависит от dxBar)
     */
    private int stepMouse;
    /**
     * Скорость передвижения графика (вводит пользователь)
     */
    private int speedStepMouse;
    /**
     * То же самое для колесика
     */
    private int speedMouseWheel;
    /**
     * Переменная используется в методе mouseDragged()
     */
    private int startMouseX, startMouseY;
    private int mouseX, mouseY;
    private int widthPriceField;
    private int heightDateField;
    //количество чисел после запятой
    private int numAfterPoint;
    private int firstX, firstY;
    /**Количество пикселей рядом с линией, при попадании на которые линия выделяется*/
    private int dSelect;
    private int radiusSelectLinePoint;
    private int selectLineIndex;
    
    private int px, pt;
    
    public static float onePips = 0.0001f;
    private float minPriceOnPanel, maxPriceOnPanel;

    private boolean newCoursor;
    private boolean pressOnNewCoursor;
    private boolean move;
    private boolean ctrlOn;
    private boolean spaceOn;
    /**Обработано ли нажатие*/
    private boolean spaceHandlerInd;
    private boolean firstPointOn, secondPointOn;
    private boolean cursorOutOfChartSize;
    private boolean oneSelectedLine;
    
    private String defaultPath;
    private String fullPath;

    /**
     * Расцветка баров
     */
    private Color colorFillBuyBar, colorFillSellBar, colorBorderBuyBar, colorBorderSellBar;
    private Color colorNewCoursor;
    private Color colorBackground;
    private Chart chart;
    private ArrayList<Line> arrLines;
    private SearchForTemplate searchT;

    /**
     * Переменные для работы с областью даты
     */
    private int widthText;
    private int startWidthText;
    private int heightText;
    private int totalDateWords;
    private int totalBarsInWord;
    /**
     * Переменные для работы с областью цен
     */
    private int totalPriceWords;
    private int startHeightStep;
    private int heightStep;
    private float priceStep;
    
    private MainFrame mainFrame;

    private Font font;
    // </editor-fold> 
    ChartPanel(int width, int height, MainFrame mf) throws IOException {
        super();
        this.setSize(width, height);
        mainFrame = mf;
        this.setFocusable(true);
                
        arrLines = new ArrayList<Line>();
        colorFillBuyBar = Color.GREEN;
        colorFillSellBar = Color.RED;
        colorBorderBuyBar = Color.BLACK;
        colorBorderSellBar = Color.BLACK;
        colorNewCoursor = Color.BLACK;
        colorBackground = Color.WHITE;
        this.setBackground(colorBackground);
        
        heightDateField = 20;
        widthPriceField = 60;
        widthBar = 6;
        mnojitel = 3;
        rightIndent = 0;
//        onePips = 0.0001f;
        yIndent = 30;
        speedStepMouse = 5;
        speedMouseWheel = 3;
        dSelect = 5;
        radiusSelectLinePoint = 4;

        defaultPath = "C:\\Николай\\Программирование\\Программы на Java\\FinanceMarket\\Charts";
//        String pathPrices = "C:\\Николай\\Программирование\\Программы на Java\\FinanceMarket\\temp.txt";
        String pathPrices = defaultPath+"\\EURUSD\\EURUSD_M1_2014-09-03_2016-07-01.txt";
//        String pathPrices = defaultPath+"\\EURUSD\\EURUSD_D1_2000-01-05_2016-08-13.txt";
        chart = new Chart(pathPrices);
//        chart = new Chart();
//        chart.readChart(pathPrices);

        stepMouse = widthBar / speedStepMouse;
        calculateNumAfterPoint();

        /*********************************************************************************************************/
        /*** Вычисление основных параметров для отрисовки области даты и котировок*/
        font = new Font("Arial", Font.PLAIN, 10);
        FontMetrics fm = getFontMetrics(font);
        heightText = fm.getHeight();
        startWidthText = 80;
        widthText = startWidthText;
        startHeightStep = 50;
        
//        //устанавливаем startArrIndex и endArrIndex
        calculateStartEndArrIndex();
        calculateOptionDateField();
        /********************************************************************************************************/
        arrLines.add(new Line(50,300,120,150,5, false));
//        arrLines.get(0).setMinCountPips(12);
//        arrLines.get(0).setMaxCountPips(12);
//        arrLines.get(0).setDeviation(1.5f);

        arrLines.add(new Line(120,150,170,250,0,false));
//        arrLines.add(new Line(120,150,170,250,0,1,false));
//        arrLines.add(new Line(170,250,220,100,4, false));
        
//        arrLines.add(new Line(250,150,400,10));
        
//        for(int i=0; i<arrLines.size();i++){
//            System.out.println("i = "+i+"; name = "+arrLines.get(i).getName());
//        }
        searchT  = new SearchForTemplate(chart.getArrBars(), arrLines);
//        searchT.findAllTemplates(arrLines);
        
        /********************************************************************************************************/
        this.addMouseMotionListener(this);
        this.addMouseListener(this);
        this.addMouseWheelListener(this);
        this.addKeyListener(this);
    }
//<editor-fold defaultstate="collapsed" desc="Get/Set Functions">    
    public int getLimitBarsOnPanel() {
        return limitBarsOnPanel;
    }
    public int getWidthBar() {
        return this.widthBar;
    }
    public int getDdx() {
        return this.ddx;
    }
    public int getStartArrIndex() {
        return this.startArrIndex;
    }
    public int getEndArrIndex() {
        return this.endArrIndex;
    }
    public int getYIndent() {
        return this.yIndent;
    }
    public float getMinPriceOnPanel() {
        return this.minPriceOnPanel;
    }
    public float getMaxPriceOnPanel() {
        return this.maxPriceOnPanel;
    }
    public float getOnePips() {
        return this.onePips;
    }
    public String getDefaultPath(){
        return this.defaultPath;
    }
    public String getFullPath(){
        return this.fullPath;
    }
    public Chart getChart() {
        return this.chart;
    }
    public int getSelectLineIndex() {
        return selectLineIndex;
    }
    public ArrayList<Line> getArrLines() {
        return arrLines;
    }
    
    public void setDdx(int ddx) {
        this.ddx = ddx;
    }
    public void setNewCoursor(boolean b) {
        this.newCoursor = b;
    }
    public void setDefaultPath(String path){
        this.defaultPath = path;
    }
    public void setFullPath(String path){
        this.fullPath = path;
    }
//</editor-fold>    

//<editor-fold defaultstate="collapsed" desc="Calculate Options and Other">
    public void calculateStartEndArrIndex(){
        //устанавливаем startArrIndex и endArrIndex
        if (chart.getArrBars().size() > limitBarsOnPanel) {
            startArrIndex = chart.getArrBars().size() - limitBarsOnPanel;
        } else {
            startArrIndex = 0;
        }
        endArrIndex = chart.getArrBars().size() - 1;
    }
    private void calculateLimitBarsOnPanel() {
        limitBarsOnPanel = (int) (getWidth() - widthPriceField) / (widthBar + ddx);
    }
    private void calculateDdx() {
        int mn = 4;
        if ((int) (widthBar / mn) < 2) {
            ddx = 2;
        } else {
            this.ddx = (int) widthBar / mn;
        }
    }
    private void calculateNumAfterPoint() {
        String str;
        int tmp = (int) Math.ceil(1 / onePips);
        str = "" + tmp;
        numAfterPoint = str.length() - 1;
    }
    private void calculateMinMaxPrices() {
        float minPrice = chart.getArrBars().get(startArrIndex).getPriceL();
        float maxPrice = chart.getArrBars().get(startArrIndex).getPriceH();
        if (endArrIndex - startArrIndex > 0) {
            for (int i = 1; i <= (endArrIndex - startArrIndex); i++) {
                if (chart.getArrBars().get(startArrIndex + i).getPriceL() < minPrice) {
                    minPrice = chart.getArrBars().get(startArrIndex + i).getPriceL();
                }
                if (chart.getArrBars().get(startArrIndex + i).getPriceH() > maxPrice) {
                    maxPrice = chart.getArrBars().get(startArrIndex + i).getPriceH();
                }
            }
        }
        this.minPriceOnPanel = minPrice;
        this.maxPriceOnPanel = maxPrice;
    }
    private void calculateDyBar() {
        try {
//            this.dyBar = (int) ((this.getHeight() - 2 * this.yIndent - heightDateField) / (int) Math.ceil(((maxPriceOnPanel - minPriceOnPanel) / this.onePips)));
            this.dyBar = Math.round ((this.getHeight() - 2 * this.yIndent - heightDateField) / (int) Math.ceil(((maxPriceOnPanel - minPriceOnPanel) / this.onePips)));
        } catch (ArithmeticException are) {
            System.out.println("" + are + "\nИсключение при вызове метода calculateDyBar() класса ChartPanel: "
                    + "Деление на ноль. Либо максимальная и минимальная цены, отображаемые на панели, совпадают, "
                    + "либо onePips=0, т.е. не корректное определение одного пункта\n"+"maxPriceOnPanel - minPriceOnPanel = "+(maxPriceOnPanel - minPriceOnPanel)+"; onePips = "+onePips);
        }
        /****************************************************************************/
        int heightPrice = (int) Math.ceil((maxPriceOnPanel - minPriceOnPanel) / this.onePips);
        int heightPx = this.getHeight() - 2 * this.yIndent - heightDateField;
        //выделяем дробную часть
        float ost;
        if( heightPrice > heightPx ){
            ost = (float) heightPrice / heightPx-(int)( heightPrice / heightPx);
//            System.out.println("heightPrice = "+heightPrice+"; heightPx = "+heightPx);
//            System.out.println("ost = "+ost);
            if( ost == 0) dyBar = 1;
            else
            if( ost <= 0.25) dyBar = 4;
            else    
            if( ost <= 0.33) dyBar = 3;
            else    
            if( ost <= 0.5) dyBar = 2;
            else    
            if( ost <= 0.67) dyBar = 3;
            else    
            if( ost <= 0.75) dyBar = 4;
            else    
            if( ost > 0.75){
                dyBar = 1;
                this.pt = (int) Math.ceil((float)heightPrice / heightPx);
            }  
            if(ost<=0.75)
                pt = (int) Math.ceil( (float)(dyBar * heightPrice) / heightPx );
        }
        else{
//            System.out.println("zashel");
//            System.out.println("heightPrice = "+heightPrice+"; heightPx = "+heightPx);
            ost = (float) heightPx / heightPrice - (int)( heightPx / heightPrice);
            if( ost == 0) pt =1 ;
            else
            if( ost <= 0.25) pt = 4;
            else    
            if( ost <= 0.33) pt = 3;
            else    
            if( ost <= 0.5) pt = 2;
            else    
            if( ost <= 0.67)pt = 3;
            else    
            if( ost <= 0.75) pt = 4;
            else    
            if( ost > 0.75){
                pt = 1;
                this.dyBar = (int) Math.ceil((float)heightPx / heightPrice );
            }  
            if(ost<=0.75)
                dyBar = (int) Math.ceil( (float)(pt * heightPx ) / heightPrice );
            
            for(int i=0;i<10;i++){
                if( (float)heightPrice / pt * dyBar > heightPx){
                    if( pt < 4){
                        pt++;
                        dyBar = (int) Math.ceil( (float)(pt * heightPx ) / heightPrice );
                    }else{
                        dyBar = (int) Math.floor( (float)(pt * heightPx ) / heightPrice );
                    }
                        
                }else
                    break;
            }
        }
//        System.out.println("dyBar = "+dyBar+"; pt = "+pt);
    }
    private void calculateHeightStep() {
//        System.out.println("dy = "+dyBar);
        if (startHeightStep > dyBar) {
            heightStep = (Math.round(startHeightStep / this.dyBar)) * dyBar;
        } else
        {
            heightStep = dyBar;
        }
    }
    private void calculateTotalPriceWords() {
        totalPriceWords = Math.round((float) ((getHeight() - yIndent - heightDateField) / this.heightStep)) + 1;
    }
    private void calculatePriceStep() {
//        priceStep = heightStep / dyBar *onePips;
        priceStep = heightStep / dyBar * pt *onePips;
    }
    private void calculateWidthText() {
        if (startWidthText >= widthBar + ddx) {
            widthText = (int) ((startWidthText / (widthBar + ddx) + 1) * (widthBar + ddx));
        } else {
            widthText = widthBar + ddx;
        }
    }
    private void calculateTotalDateWords() {
        if (limitBarsOnPanel > 1) {
            totalDateWords = (int) ((limitBarsOnPanel * (widthBar + ddx) - widthBar / 2) / widthText) + 1;
        } else {
            totalDateWords = 1;
        }
    }
    private void calculateTotalBarsInWord() {
        totalBarsInWord = widthText / (widthBar + ddx);
    }
    private void calculateStartEndIndex() {
        if( this.chart.getArrBars().size() >= startArrIndex+limitBarsOnPanel)
            endArrIndex = startArrIndex+limitBarsOnPanel-1;
        else
        if( this.chart.getArrBars().size() < startArrIndex+limitBarsOnPanel ){
            endArrIndex = chart.getArrBars().size() - 1;
            if( endArrIndex - limitBarsOnPanel+1 >= 0 )
                startArrIndex = endArrIndex - limitBarsOnPanel+1;
            else
                startArrIndex = 0;
        }
        else
        if( chart.getArrBars().size() > limitBarsOnPanel ){
            startArrIndex = endArrIndex - limitBarsOnPanel+1;
        }
        else
        {
            startArrIndex = 0;
            endArrIndex = chart.getArrBars().size()-1;
        }
    }
    public void calculateOptionDateField() {
        calculateDdx();
        calculateLimitBarsOnPanel();
        calculateStartEndIndex();
        calculateWidthText();
        calculateTotalDateWords();
        calculateTotalBarsInWord();
    }
    public void calculateOptionPriceField() {
        calculateMinMaxPrices();
        calculateDyBar();
        calculateHeightStep();
        calculateTotalPriceWords();
        calculatePriceStep();
    }

    private String normalizeIntToString(int a) {
        String str;
        str = "" + a;
        if (a < 10) {
            str = "0" + str;
        }
        return str;
    }
    private String normalizeFloatToString(float a) {
        a = new BigDecimal(a).setScale(numAfterPoint, RoundingMode.HALF_UP).floatValue();
        String str;
        str = "" + a;
        int n = numAfterPoint + 2 - str.length();
        for (int i = 0; i < n; i++) {
            str = str + '0';
        }
        return str;
    }

        /**
     * @param n - количество баров, на которое смещается график
     */
    private void moveLeftChart(int n) {
        if (startArrIndex - n >= 0) {
            move = true;
            startArrIndex = startArrIndex - n;
            endArrIndex = endArrIndex - n;
        } else {
            endArrIndex = endArrIndex - startArrIndex;
            startArrIndex = 0;
        }
        calculateOptionPriceField();
    }
    private void moveRightChart(int n) {
        if (endArrIndex + n <= chart.getArrBars().size() - 1) {
            move = true;
            endArrIndex = endArrIndex + n;
            startArrIndex = startArrIndex + n;
        } else {
            startArrIndex = startArrIndex + (chart.getArrBars().size() - 1 - endArrIndex);
            endArrIndex = chart.getArrBars().size() - 1;
        }
        calculateOptionPriceField();
    }
//</editor-fold> 

//<editor-fold defaultstate="collapsed" desc="Work with Select Lines and drawLines">    
    public boolean isOneSelectedLine(){
        return this.oneSelectedLine;
    }
    /**
     * @param rectX - координата x вертикальной линии квадратика
     * @param my - координата мыши по y ( исходя из нее расчитываются rectDownY и rectUpY )
     */
    private boolean isVerticalGeneralPoint( int rectX, int my, Line line){
        int rectDownY = my + dSelect;
        int rectUpY = my - dSelect;
        /**Если линия горизонтальная*/
        if( line.getY1() == line.getY2() ){
            if( rectX >= line.getX1() && rectX <= line.getX2() && line.getY1() >= rectUpY && line.getY1() <= rectDownY )
                return true;
            else
                return false;
        }
        /**Если находится внутри диапазона линии*/
        if( rectX >= line.getX1() && rectX <= line.getX2() &&
                ( line.isBuyLine() && rectUpY <= line.getY1() && rectDownY >= line.getY2()
                || line.isSellLine() && rectDownY >= line.getY1() && rectUpY <= line.getY2() ) ) {
            int b;
            int pointY = 0;
            float k;
            k = (float)((float)Math.abs( line.getY1() - line.getY2()) / (float)Math.abs( line.getX1() - line.getX2() ) );
            if( line.isBuyLine() ){
                b = Math.round(line.getY1() + k*line.getX1());
                pointY = Math.round( -k*rectX + b);
            }else   
            if( line.isSellLine() ){
                b = Math.round(line.getY1() - k*line.getX1());
                pointY = Math.round(k*rectX + b);
            }    
            if( pointY >= rectUpY && pointY <= rectDownY )
                return true;
        }
        return false;
    }
    private boolean isHorizontalGeneralPoint( int rectY, int mx, Line line){
        int rectLeftX = mx - dSelect;
        int rectRightX = mx + dSelect;
        /**Если находится внутри диапазона линии*/
        if( ( rectY >= line.getY1() && rectY <= line.getY2() 
                || rectY <= line.getY1() && rectY >= line.getY2() ) &&
                ( rectLeftX <= line.getX2() && rectRightX >= line.getX1()
                ) ){
            int b;
            int pointX = 0;
            
            float k;
            k = (float)Math.abs( line.getY1() - line.getY2() ) / (float)Math.abs( line.getX1() - line.getX2() );
            
            if(line.isBuyLine()){
                b = line.getX1() - Math.round( line.getY1() / (-k) );
                pointX = Math.round( rectY / (-k) + b );
            }else
            if(line.isSellLine()){
                b = line.getX1() - Math.round( line.getY1() / k );
                pointX = Math.round( rectY / k + b );
            }
            
            if( pointX >= rectLeftX && pointX <= rectRightX )
                return true;
        }
        return false;
    }
    private boolean isClickedOnLine(int mx, int my, Line line){
        /**Если линиия меньше курсора*/
        if( line.getX1() >= mx-dSelect && line.getX2() <= mx+dSelect 
                && ( line.isBuyLine() && line.getY1() < my+dSelect && line.getY2() > my-dSelect 
                    || line.isSellLine() && line.getY1() > my-dSelect && line.getY2() < my+dSelect ) )
            return true;
        
        
        if ( isVerticalGeneralPoint(mx+dSelect, my, line) || isVerticalGeneralPoint(mx-dSelect, my, line)
                || isHorizontalGeneralPoint(my+dSelect, mx, line) || isHorizontalGeneralPoint(my-dSelect, mx, line) ) 
            return true;
        return false;
    }
        public void drawLines(MouseEvent mpe){
        //Отрисовка линий по нажатию левой кнопки мыши без нажатой клавиши CTRL
        if (mpe.getModifiers() == MouseEvent.BUTTON1_MASK && spaceOn && !firstPointOn && !secondPointOn 
                && !this.cursorOutOfChartSize && !ctrlOn) {
            firstX = mpe.getX() / ( ddx+widthBar )*( ddx+widthBar );
            firstY = mpe.getY();
            mouseX = firstX;
            mouseY = mpe.getY();
            firstPointOn = true;
            repaint();
        }else 
        if(mpe.getModifiers() == MouseEvent.BUTTON1_MASK && spaceOn && firstPointOn && !this.cursorOutOfChartSize && !ctrlOn){
            mouseX = mpe.getX() / ( ddx+widthBar )*( ddx+widthBar )+widthBar+ddx;
            mouseY = mpe.getY();
            if( firstX != mouseX ){
                firstPointOn = false;
                secondPointOn = true;
                arrLines.add(new Line(firstX, firstY, mouseX, mouseY));
            }
            repaint();
        }
/************************************/
        //Отрисовка линий по нажатию левой кнопки мыши одновременно с нажатой клавишей CTRL
        if(ctrlOn )
            firstPointOn = true;
        /**Если линия не первая*/
        if( mpe.getModifiers() == (MouseEvent.BUTTON1_MASK+MouseEvent.CTRL_MASK) && spaceOn && firstPointOn && ctrlOn 
                && !this.cursorOutOfChartSize && !arrLines.isEmpty()){
            mouseX = mpe.getX() / ( ddx+widthBar )*( ddx+widthBar )+widthBar+ddx;
            mouseY = mpe.getY();
            if(mouseX > firstX){
                secondPointOn = true;
                arrLines.add(new Line(firstX, firstY, mouseX, mouseY));
            }
            repaint();
        }
        else
        if(mpe.getModifiers() == (MouseEvent.BUTTON1_MASK+MouseEvent.CTRL_MASK) && spaceOn 
                && firstPointOn && !secondPointOn && ctrlOn && !this.cursorOutOfChartSize && arrLines.isEmpty()){
            firstX = mpe.getX() / ( ddx+widthBar )*( ddx+widthBar )+widthBar+ddx;
            firstY = mpe.getY();
            mouseX = firstX;
            mouseY = mpe.getY();
            repaint();   
        }
        
    }
    /**Выделить (выбрать) линию*/
    private void selectLine(MouseEvent e){
        boolean click;
        if( spaceOn && (e.getModifiers() == MouseEvent.BUTTON1_MASK+MouseEvent.SHIFT_MASK) ){
            for (int i = 0; i < arrLines.size(); i++) {
                click = isClickedOnLine(e.getX(), e.getY(), this.arrLines.get(i));
                if(click){
                    this.arrLines.get(i).setSelect( !arrLines.get(i).isSelect() );
                    repaint();
                    break;
                }
            }
            calculateOneSelectedLine();
            mainFrame.setAccessToOptionPanel(this.oneSelectedLine);
            
            if(this.oneSelectedLine){
                mainFrame.gettName().setText( arrLines.get(this.selectLineIndex).getName() );
                mainFrame.gettMinCountBars().setText( String.valueOf( arrLines.get(this.selectLineIndex).getMinCountBar() ) );
                mainFrame.gettMaxCountBars().setText( String.valueOf( arrLines.get(this.selectLineIndex).getMaxCountBar() ) );
                mainFrame.gettMinCountPips().setText( String.valueOf( arrLines.get(this.selectLineIndex).getMinCountPips() ) );
                mainFrame.gettMaxCountPips().setText( String.valueOf( arrLines.get(this.selectLineIndex).getMaxCountPips() ) );                
                mainFrame.gettFather().setText( arrLines.get(this.selectLineIndex).getForeignName() );
                
                mainFrame.isChbStrongLine().setSelected( arrLines.get(this.selectLineIndex).isStrongLine() );
                
                if( arrLines.get(this.selectLineIndex).getForeignName() == null || arrLines.get(this.selectLineIndex).getForeignName().equals("") )
                    mainFrame.isChbForeignLine().setSelected(false);
                else
                    mainFrame.isChbForeignLine().setSelected(true);
                
                mainFrame.setAccessToForeignLine();
                if( !arrLines.get(this.selectLineIndex).isImportantPips()){
                    mainFrame.gettMinCountPips().setEnabled(false);
                    mainFrame.gettMaxCountPips().setEnabled(false);
                }
            }
        }
    }
    private void calculateOneSelectedLine(){
        int nSelectedLine = 0;
            for (int i = 0; i < arrLines.size(); i++) {
                if (arrLines.get(i).isSelect()) {
                    this.selectLineIndex = i;
                    nSelectedLine++;
                }
                if(nSelectedLine > 1){
                    oneSelectedLine = false;
                    this.selectLineIndex = -1;
                    break;
                }
            }
            if( nSelectedLine == 0)
                oneSelectedLine = false;
            if( nSelectedLine == 1 )
                oneSelectedLine = true;
    }
//</editor-fold>    
   
//<editor-fold defaultstate="collapsed" desc="Painting">    
    private int convertFloatPriceToInt(float price){
//        return (int) (this.yIndent + Math.round((maxPriceOnPanel-price) / this.onePips) * dyBar);
        return (int) (this.yIndent + (float)Math.round((maxPriceOnPanel-price) / this.onePips) / pt * dyBar);
    }
    private void paintDateField(Graphics gr) {
        gr.setFont(font);
        gr.setColor(Color.BLACK);

        /**
         * Если количество слов, выводимых на панель меньше количества элементов
         */
        int num;
        if (chart.getArrBars().size() < this.limitBarsOnPanel) {
            num = (int) Math.ceil(chart.getArrBars().size() / this.totalBarsInWord);
            if (num == 0) {
                num = 1;
            }
        } else {
            num = totalDateWords;
        }
        try {
                for (int i = 0; i < num && (startArrIndex + i * totalBarsInWord) < chart.getArrBars().size(); i++) {
                    if(!mainFrame.getBGroup().getSelection().getActionCommand().equals("D1") 
                        && !mainFrame.getBGroup().getSelection().getActionCommand().equals("W1")
                        && !mainFrame.getBGroup().getSelection().getActionCommand().equals("MN")){
                    gr.drawString("" + normalizeIntToString(chart.getArrBars().get(this.startArrIndex + i * totalBarsInWord).getDate().getDay())
                            + " " + chart.getArrBars().get(startArrIndex + i * totalBarsInWord).getDate().convertMonthToString()
                            + " " + normalizeIntToString(chart.getArrBars().get(startArrIndex + i * totalBarsInWord).getDate().getHour())
                            + ":" + normalizeIntToString(chart.getArrBars().get(startArrIndex + i * totalBarsInWord).getDate().getMinute())
                            + "", widthBar / 2 + i * widthText, this.getHeight() - heightDateField + 15);
                    }
                    else{
                        gr.drawString("" + normalizeIntToString(chart.getArrBars().get(this.startArrIndex + i * totalBarsInWord).getDate().getDay())
                            + " " + chart.getArrBars().get(startArrIndex + i * totalBarsInWord).getDate().convertMonthToString()
                            + " " + chart.getArrBars().get(this.startArrIndex + i * totalBarsInWord).getDate().getYear()
                                , widthBar / 2 + i * widthText, this.getHeight() - heightDateField + 15);
                    }
                    gr.drawLine(widthBar / 2 + i * widthText, this.getHeight() - heightDateField, widthBar / 2 + i * widthText, this.getHeight() - heightDateField + 3);
                }
        } catch (ArrayIndexOutOfBoundsException aie) {
            System.out.println("" + aie + "\nИсключение в методе paintPriceField класса ChartPanel: выход за пределы массива");
        } catch (IndexOutOfBoundsException ie) {
            System.out.println("" + ie + "\nИсключение в методе paintPriceField класса ChartPanel: выход индекса за допустимые пределы");
        } catch (Exception e) {
            System.out.println("" + e + "\nИсключение в методе paintPriceField класса ChartPanel");
        }
    }
    private void paintPriceField(Graphics gr) {
        String text;
        gr.setFont(font);
        gr.setColor(Color.BLACK);
        try {
            for (int i = 0; i < totalPriceWords; i++) {
//                System.out.println("i = "+i+"; totalPriceWords = "+totalPriceWords+"; getWidth() = "+getWidth()+"; widthPriceField = "+widthPriceField
//                +"; priceStep = "+priceStep+"; heightStep = "+heightStep);
                text = normalizeFloatToString(maxPriceOnPanel - i * this.priceStep);

                gr.drawString("" + text, this.getWidth() - widthPriceField + 5, yIndent + i * heightStep);
                gr.drawLine(this.getWidth() - widthPriceField, yIndent + i * heightStep, this.getWidth() - widthPriceField + 3, yIndent + i * heightStep);
            }
        } catch (ArrayIndexOutOfBoundsException aie) {
            System.out.println("" + aie + "\nИсключение в методе paintPriceField класса ChartPanel: выход за пределы массива");
        } catch (IndexOutOfBoundsException ie) {
            System.out.println("" + ie + "\nИсключение в методе paintPriceField класса ChartPanel: выход индекса за допустимые пределы");
        } catch (Exception e) {
            System.out.println("" + e + "\nИсключение в методе paintPriceField класса ChartPanel");
        }
    }
    private void paintBar(Graphics gr, int index) {
        Bar bar = chart.getArrBars().get(index);

        int x, y;
        int yL, yH;
        int height;

        int heightPrice = (int) Math.ceil((maxPriceOnPanel - minPriceOnPanel) / this.onePips);
        int heightPx = this.getHeight() - 2 * this.yIndent - heightDateField;
//        System.out.println("heightPrice = "+heightPrice+"; heightPx = "+heightPx);
//            System.out.println("dyBar = "+dyBar+"; pt = "+pt);
            yL = (int)( this.yIndent + (float)Math.round((maxPriceOnPanel - bar.getPriceL()) / this.onePips) / pt * dyBar );
            yH = (int)( this.yIndent + (float)Math.round((maxPriceOnPanel - bar.getPriceH()) / this.onePips) / pt * dyBar );
            int i = index - this.startArrIndex;
            x = (ddx + widthBar) * i;
            height = (int) ((float)Math.round(abs(bar.getPriceC() - bar.getPriceO()) / onePips) / pt * dyBar);
            
            if (widthBar == 1) {
                gr.drawLine(x + widthBar / 2, yL, x + widthBar / 2, yH);
            } else /**
             * Если свечка бычья, то ...
             */
            if (bar.getPriceC() >= bar.getPriceO()) {
                gr.setColor(colorBorderBuyBar);
                y = (int) (this.yIndent + (float)Math.round((maxPriceOnPanel - bar.getPriceC()) / this.onePips) / pt * dyBar);

                gr.drawRect(x, y, widthBar, height);
                if (bar.getPriceC() != bar.getPriceH()) {
                    gr.drawLine(x + widthBar / 2, y, x + widthBar / 2, yH);
                }
                if (bar.getPriceO() != bar.getPriceL()) {
                    gr.drawLine(x + widthBar / 2, y + height, x + widthBar / 2, yL);
                }

                gr.setColor(colorFillBuyBar);
                gr.fillRect(x + 1, y + 1, widthBar - 1, height - 1);

            } else {
                gr.setColor(colorBorderSellBar);
                y = (int) (this.yIndent + (float)Math.round((maxPriceOnPanel - bar.getPriceO()) / this.onePips) / pt * dyBar);
                gr.drawRect(x, y, widthBar, height);
                if (bar.getPriceO() != bar.getPriceH()) {
                    gr.drawLine(x + widthBar / 2, y, x + widthBar / 2, yH);
                }
                if (bar.getPriceC() != bar.getPriceL()) {
                    gr.drawLine(x + widthBar / 2, y + height, x + widthBar / 2, yL);
                }

                gr.setColor(colorFillSellBar);
                gr.fillRect(x + 1, y + 1, widthBar - 1, height - 1);
            }
    }
    private void paintBarField(Graphics gr){
        gr.setColor(Color.BLACK);
        gr.drawRect(0, 0, this.getWidth() - 1 - widthPriceField, this.getHeight() - 1 - heightDateField);
        try {
            for (int i = 0; i <= (endArrIndex - startArrIndex); i++) {
                paintBar(gr, startArrIndex + i);
            }
        } catch (ArrayIndexOutOfBoundsException aie) {
            System.out.println("" + aie + "\nИсключение в методе paintBarField() класса ChartPanel: выход за пределы массива");
        }
    }
    private void paintCursor(Graphics gr) {
        String text;
        int tempX = mouseX, tempY = mouseY;
        int barsCount;
        int pipsCount;
        if (newCoursor) {
            if( pressOnNewCoursor ){
                mouseX = startMouseX;
                mouseY = startMouseY;
            }
                
            gr.setColor(colorNewCoursor);
            gr.drawLine(0, mouseY, this.getWidth() - this.widthPriceField+3, mouseY);
            gr.drawLine(mouseX, 0, mouseX, this.getHeight() - this.heightDateField + 3);
            gr.fillRect(this.getWidth() - widthPriceField+3, mouseY - heightText / 2, widthPriceField, this.heightText);
            gr.fillRect(mouseX - 50, this.getHeight() - this.heightDateField + 3, 95, heightText);
            
            /** рисуем линию, кол-во баров и пунктов*/
            if( pressOnNewCoursor ){
                gr.drawLine( startMouseX, startMouseY, tempX, tempY);
                gr.setColor(colorBackground);
                gr.fillRect(tempX, tempY-heightText+3, 50, this.heightText);
                gr.setColor(colorNewCoursor);
                
                if( tempX >= startMouseX ){
                    barsCount = tempX / ( ddx+widthBar ) - startMouseX / ( ddx+widthBar ) + 1;
                }else{
                    barsCount = startMouseX / ( ddx+widthBar ) - tempX / ( ddx+widthBar ) + 1;
                }
                pipsCount =0;
                if( tempY >= startMouseY ){
                    pipsCount = tempY / dyBar*pt - ( startMouseY ) / dyBar*pt;
                }
                else{
                    pipsCount = startMouseY / dyBar*pt - tempY / dyBar*pt;
                }
                text=""+barsCount+" / "+pipsCount;
                gr.drawString(text, tempX+7, tempY);
            }
            
            gr.setColor(this.colorBackground);
            int i;
            i = this.startArrIndex + Math.round(mouseX / (ddx + widthBar));
            if (i >= chart.getArrBars().size()) {
                i = this.endArrIndex;
            }

            text = "" + chart.getArrBars().get(i).getDate().getYear() + "." + normalizeIntToString(chart.getArrBars().get(i).getDate().getMonth())
                    + "." + normalizeIntToString(chart.getArrBars().get(i).getDate().getDay()) + " " + chart.getArrBars().get(i).getDate().getHour()
                    + ":" + normalizeIntToString(chart.getArrBars().get(i).getDate().getMinute());
            gr.drawString(text, mouseX - 45, this.getHeight() - this.heightDateField + heightText);

            //Если курсор находится в yIndent, то..., иначе ...
                if (mouseY < yIndent ) {
                    text = normalizeFloatToString(this.maxPriceOnPanel + Math.round(((yIndent - mouseY) / dyBar) * pt) * onePips);
                } else {
                    if( mouseY - yIndent < dyBar)
                        text = normalizeFloatToString(this.maxPriceOnPanel - (float)pt * onePips);
                    else{
                        text = normalizeFloatToString(this.maxPriceOnPanel - Math.round(((mouseY - yIndent) / dyBar+1) * pt) * onePips );
                    }
                }
            gr.drawString(text, this.getWidth() - widthPriceField + 5, mouseY + heightText / 2 - 2);
            
        }
    }
    private void paintLines(Graphics gr2){
        Graphics2D gr = (Graphics2D) gr2; 
        gr.setStroke(new BasicStroke(1.5f));  
//        gr.setRenderingHint( RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);
//        gr.setRenderingHint(RenderingHints.KEY_TEXT_ANTIALIASING,RenderingHints.VALUE_TEXT_ANTIALIAS_ON);
        
        gr.setColor(Color.RED);
        for(int i=0;i<arrLines.size();i++){
            if( arrLines.get(i).isSelect() && spaceOn){
                    gr.fillOval(arrLines.get(i).getX1()-radiusSelectLinePoint, arrLines.get(i).getY1()-radiusSelectLinePoint, radiusSelectLinePoint*2, radiusSelectLinePoint*2);
                    gr.fillOval(arrLines.get(i).getX2()-radiusSelectLinePoint, arrLines.get(i).getY2()-radiusSelectLinePoint, radiusSelectLinePoint*2, radiusSelectLinePoint*2);
                    gr.fillOval(arrLines.get(i).getMidX()-radiusSelectLinePoint, arrLines.get(i).getMidY()-radiusSelectLinePoint, radiusSelectLinePoint*2, radiusSelectLinePoint*2);
            }
            gr.drawLine(arrLines.get(i).getX1(), arrLines.get(i).getY1(), arrLines.get(i).getX2(), arrLines.get(i).getY2());
        }
        
        if(spaceOn && firstPointOn || spaceOn && secondPointOn){
            gr.drawLine(firstX, firstY, mouseX, mouseY);
            secondPointOn = false;
        }
/************************************/  
        //если нажата CTRL
        if(spaceOn && firstPointOn && ctrlOn && !arrLines.isEmpty() || spaceOn && !secondPointOn && ctrlOn && !arrLines.isEmpty()){
            firstX = arrLines.get(arrLines.size()-1).getX2();
            firstY = arrLines.get(arrLines.size()-1).getY2();
            gr.drawLine(firstX, firstY, mouseX, mouseY);
            secondPointOn = false;
        }   
//        gr.setStroke(new BasicStroke(1)); 
    }
    
    private int calculateY1LineBetweenLeftBorder( LineOnChart line ){
        int y1 = 0; 
        float k;
        k = Math.round(( convertFloatPriceToInt( line.getMinPrice())-convertFloatPriceToInt( line.getMaxPrice()) ) / line.getCountBar());
        int countOutsideBars = this.startArrIndex-line.getStartBar();

        if( line.isBuyLine() )
            y1 = convertFloatPriceToInt( line.getMaxPrice()) + Math.round( ( line.getCountBar()-countOutsideBars ) * k );
        else
        if( line.isSellLine() ) 
            y1 = convertFloatPriceToInt( line.getMinPrice()) - Math.round( ( line.getCountBar()-countOutsideBars ) * k );
        return y1;
    }
    private int calculateY2LineBetweenLeftBorder( LineOnChart line ){
        int y2 = 0;

        if( line.isBuyLine() )
            y2 = convertFloatPriceToInt( line.getMaxPrice() );
        else
        if( line.isSellLine()) 
            y2 = convertFloatPriceToInt( line.getMinPrice() );
        return y2;
    }
    /**
     * 
     * @param line_1 Линия, предшедствующая line. Если предшедствующей линии нет, то line_1 = null
     * @param line Линия, для которой вычисляется параметр
     * @param indexInTemplate Номер линии в шаблоне, начинаяя с "0"
     * @return y1 Возвращает y1
     */
    private int calculateY1LineBetweenRightBorder(LineOnChart line_1, LineOnChart line, int indexInTemplate){
        int y1=0;                
        if(line.isBuyLine()){
            if(indexInTemplate!=0 ){
                if( line_1.isBuyLine() )
                    y1 = convertFloatPriceToInt(line_1.getMaxPrice() );
                else
                if( line_1.isSellLine() )
                    y1 = convertFloatPriceToInt(line_1.getMinPrice() );
            }
            else
                y1 = convertFloatPriceToInt(line.getMinPrice() );                  
        }
        else
        if(line.isSellLine()) {
            if(indexInTemplate!=0){
                if(line_1.isBuyLine())
                    y1 = convertFloatPriceToInt(line_1.getMaxPrice() );
                else
                if( line_1.isSellLine() )
                    y1 = convertFloatPriceToInt(line_1.getMinPrice() );
            }
            else
                y1 = convertFloatPriceToInt(line.getMaxPrice() );
        }
        return y1;
    }
    private int calculateY2LineBetweenRightBorder(LineOnChart line_1, LineOnChart line, int indexInTemplate){
        int y2 = 0;
        float k = 0.0f;
        int countOutsideBars = line.getStartBar()+line.getCountBar()-1-this.endArrIndex;

        if(line.isBuyLine()){
            if(indexInTemplate!=0 ){
                if(line_1.isBuyLine())
                    k = (float)( convertFloatPriceToInt(line_1.getMaxPrice())-convertFloatPriceToInt(line.getMaxPrice()) ) / line.getCountBar();
                else
                if(line_1.isSellLine())    
                    k = (float)( convertFloatPriceToInt(line_1.getMinPrice())-convertFloatPriceToInt(line.getMaxPrice()) ) / line.getCountBar();            
            }
            else
                k = (float)( convertFloatPriceToInt(line.getMinPrice())-convertFloatPriceToInt(line.getMaxPrice()) ) / line.getCountBar();
            y2 = convertFloatPriceToInt( line.getMaxPrice() ) + Math.round( countOutsideBars * k );
        }
        else
        if( line.isSellLine() ) {
            if( indexInTemplate != 0){
                if( line_1.isBuyLine() )
                    k = (float)( convertFloatPriceToInt( line.getMinPrice())-convertFloatPriceToInt( line_1.getMaxPrice()) ) / line.getCountBar();
                else
                if(line_1.isSellLine())
                    k = (float)( convertFloatPriceToInt( line.getMinPrice())-convertFloatPriceToInt( line_1.getMinPrice()) ) / line.getCountBar();        
            }
            else
                k = (float)( convertFloatPriceToInt( line.getMinPrice())-convertFloatPriceToInt( line.getMaxPrice()) ) / line.getCountBar();
            y2 = convertFloatPriceToInt( line.getMinPrice() ) - Math.round( countOutsideBars * k );
        }
        return y2;
    }
    private int calculateY1LineFullInsideField(LineOnChart line_1, LineOnChart line, int indexInTemplate){
        int y1 = 0;
        if( indexInTemplate == 0 ){
            if( line.isBuyLine() )
                y1 = convertFloatPriceToInt(chart.getArrBars().get( line.getStartBar() ).getPriceL());
            else
            if( line.isSellLine())
                y1 = convertFloatPriceToInt(chart.getArrBars().get( line.getStartBar() ).getPriceH());
        }
        else{
            if( line.isBuyLine() ){
                if( line_1.isBuyLine() )
                    y1 = convertFloatPriceToInt( line_1.getMaxPrice());
                else
                if( line_1.isSellLine() )    
                    y1 = convertFloatPriceToInt( line_1.getMinPrice());
            }
            else
            if( line.isSellLine() ){
                if( line_1.isBuyLine() )
                    y1 = convertFloatPriceToInt( line_1.getMaxPrice()); 
                else
                if( line_1.isSellLine() )
                    y1 = convertFloatPriceToInt( line_1.getMinPrice()); 
            }
        }
        return y1;
    }
    private int calculateY2LineFullInsideField(LineOnChart line){
        int y2 = 0;
            if( line.isBuyLine()){
                y2 = convertFloatPriceToInt( line.getMaxPrice() );
            }else
            if( line.isSellLine()){
                y2 = convertFloatPriceToInt( line.getMinPrice() );
            }
        return y2;
    }
    private void paintSearchLines(Graphics gr2){
        Graphics2D gr = (Graphics2D) gr2; 
        gr.setStroke(new BasicStroke(1.5f)); 
        gr.setColor(Color.BLACK);
        
        int x1 = 0, x2 = 0, y1 = 0, y2 = 0;
        for(int i=0; i < searchT.getArrComboLines().size(); i++)
            for(int j=0; j < searchT.getArrComboLines().get(i).size(); j++){
                /** Если линия находится на видимой части экрана*/
                if( searchT.getArrComboLines().get(i).get(j).getStartBar() >= this.startArrIndex 
                        && searchT.getArrComboLines().get(i).get(j).getStartBar()+searchT.getArrComboLines().get(i).get(j).getCountBar() -1 <= this.endArrIndex){

                    x1 = ( searchT.getArrComboLines().get(i).get(j).getStartBar() - startArrIndex )*( widthBar+ddx );
                    x2 = x1 + ( searchT.getArrComboLines().get(i).get(j).getCountBar()  )*( widthBar+ddx );
                    if(j != 0)
                        y1 = calculateY1LineFullInsideField( searchT.getArrComboLines().get(i).get(j-1), searchT.getArrComboLines().get(i).get(j), j  );
                    else
                        y1 = calculateY1LineFullInsideField( null, searchT.getArrComboLines().get(i).get(j), j  );
                    y2 = calculateY2LineFullInsideField( searchT.getArrComboLines().get(i).get(j) );
                    gr.drawLine( x1, y1, x2, y2 );
                }
                /**Если часть линии находится за левым краем панели, а другая часть на панели, то вычисляем параметры отображения*/
                if(searchT.getArrComboLines().get(i).get(j).getStartBar() < this.startArrIndex 
                        && searchT.getArrComboLines().get(i).get(j).getStartBar() + searchT.getArrComboLines().get(i).get(j).getCountBar()-1 >= this.startArrIndex){                   
                    x1 = 0;
                    x2 = ( searchT.getArrComboLines().get(i).get(j).getStartBar()+searchT.getArrComboLines().get(i).get(j).getCountBar()-this.startArrIndex)*( widthBar+ddx );

                    y1 = calculateY1LineBetweenLeftBorder( searchT.getArrComboLines().get(i).get(j) );
                    y2 = calculateY2LineBetweenLeftBorder( searchT.getArrComboLines().get(i).get(j) );
                }
                /**-------|||||||-------- для правого края*/
                if(searchT.getArrComboLines().get(i).get(j).getStartBar()  <= this.endArrIndex 
                        && searchT.getArrComboLines().get(i).get(j).getStartBar() + searchT.getArrComboLines().get(i).get(j).getCountBar()-1 > this.endArrIndex){      
                    x1 = (searchT.getArrComboLines().get(i).get(j).getStartBar()-this.startArrIndex)*( widthBar+ddx );
                    x2 = this.getWidth() - this.widthPriceField-1;
                    
                    if(j!=0){
                        y1 = calculateY1LineBetweenRightBorder(searchT.getArrComboLines().get(i).get(j-1), searchT.getArrComboLines().get(i).get(j), j);
                        y2 = calculateY2LineBetweenRightBorder(searchT.getArrComboLines().get(i).get(j-1), searchT.getArrComboLines().get(i).get(j), j);
                    }
                    else
                    {
                        y1 = calculateY1LineBetweenRightBorder(null, searchT.getArrComboLines().get(i).get(j), j);
                        y2 = calculateY2LineBetweenRightBorder(null, searchT.getArrComboLines().get(i).get(j), j);
                    } 
                }
                
                gr.drawLine(x1, y1, x2, y2);    
            }
    }

    @Override
    public void paint(Graphics gr) {
        super.paint(gr);
        if ( ctrlOn && !spaceOn ) {
            calculateOptionDateField();
            calculateOptionPriceField();
        }
        if (!newCoursor) {
            calculateOptionPriceField();
        }
//Возможно нужен:
//calculateOptionDateField();
//            calculateOptionPriceField();
        if(spaceHandlerInd){
            calculateOptionDateField();
            calculateOptionPriceField();
            spaceHandlerInd = false;
        }
        paintBarField(gr);
        if(spaceOn){
            gr.setColor(new Color(255,255,255,150));
            gr.fillRect(0, 0, getWidth(), getHeight());
        } 
        paintLines(gr);
        paintDateField(gr);
        paintPriceField(gr);
        paintCursor(gr);
        
        paintSearchLines(gr);
    }
//</editor-fold>
  
//<editor-fold defaultstate="collapsed" desc="Listeners">
    @Override
    public void mouseClicked(MouseEvent mce) { }
    @Override
    public void mousePressed(MouseEvent mpe) {        
        startMouseX = mpe.getX();
        startMouseY = mpe.getY();
        drawLines(mpe);
        selectLine(mpe);
        repaint();
        //Нажатие на колесико мыши
        if (mpe.getModifiers() == MouseEvent.BUTTON2_MASK) {
            mouseX = mpe.getX();
            mouseY = mpe.getY();
            newCoursor = true;
            pressOnNewCoursor = false;
            repaint();
        } else {
            if (newCoursor && mpe.getModifiers() == MouseEvent.BUTTON3_MASK) {
                newCoursor = false;
                repaint();
            }
        }
        if( mpe.getModifiers() == MouseEvent.BUTTON1_MASK && newCoursor)
            pressOnNewCoursor = true;
//        this.requestFocusInWindow();
        this.requestFocus();
    }
    @Override
    public void mouseReleased(MouseEvent mre) {
        if (newCoursor && mre.getModifiers() == MouseEvent.BUTTON1_MASK) {
            newCoursor = false;
            repaint();
        }
        if (mre.getModifiers() == MouseEvent.BUTTON3_MASK && spaceOn) {
            firstPointOn = false;
            secondPointOn = false;
            repaint();
        }
    }
    @Override
    public void mouseEntered(MouseEvent e) {
    }
    @Override
    public void mouseExited(MouseEvent e) {
    }
    @Override
    public void mouseDragged(MouseEvent mde) {    
        if( newCoursor ){
            mouseX = mde.getX();
            mouseY = mde.getY();
            repaint();
        }
        if (!newCoursor && !spaceOn) {
            /**
             * Если движение мыши вправо, то двигаем график влево, иначе - вправо
             */
            if (abs(mde.getX() - startMouseX) >= stepMouse && mde.getX() > startMouseX
                    && startArrIndex != 0) {
                startMouseX = mde.getX();
                moveLeftChart(1);
                repaint();
            } else if (abs(mde.getX() - startMouseX) >= stepMouse && mde.getX() < startMouseX
                    && endArrIndex != chart.getArrBars().size() - 1) {
                startMouseX = mde.getX();
                moveRightChart(1);
                repaint();
            }
        }
    }
    @Override
    public void mouseMoved(MouseEvent me) {
        if(me.getX() > (getWidth()-this.widthPriceField) || me.getY() > (getHeight()-this.heightDateField)
                    || me.getX() < 0 || me.getY() < 0)
            cursorOutOfChartSize = true;
        else
            cursorOutOfChartSize = false;
        
        if (this.newCoursor) {
            mouseX = me.getX();
            mouseY = me.getY();
            repaint();
        }
        if(firstPointOn || ctrlOn){
            
            mouseX = me.getX();
            mouseY = me.getY();
            repaint();
        }
    }
    @Override
    public void mouseWheelMoved(MouseWheelEvent mwe) {
        if(!spaceOn){
            if (mwe.getWheelRotation() > 0 && ctrlOn ) {
                if (widthBar > 1) {
                    widthBar = widthBar - 1;
                }
                repaint();
            } else {
                if (mwe.getWheelRotation() < 0 && ctrlOn) {
                    if (widthBar < getWidth() / 20) {
                        widthBar = widthBar + 1;
                    }
                    repaint();
                } else /**
                 * Если колесико крутить на себя, то двигать график влево, иначе - вправо
                 */
                {
                    if (mwe.getWheelRotation() > 0 && startArrIndex != 0) {
                        moveLeftChart(speedMouseWheel);
                    } else {
                        if (mwe.getWheelRotation() < 0 && endArrIndex != chart.getArrBars().size() - 1) {
                            moveRightChart(speedMouseWheel);
                        }
                    }
                    repaint();
                }
            }
            repaint();
        }
    }
    @Override
    public void keyTyped(KeyEvent ke) {    }
    @Override
    public void keyPressed(KeyEvent ke) {
        if (ke.getKeyCode() == KeyEvent.VK_CONTROL) {
            ctrlOn = true;
//            repaint();
        }
        if (ke.getKeyCode() == KeyEvent.VK_SPACE) {
            if(spaceOn){
                spaceOn = false;
                searchT.findAllTemplates(arrLines);
            }
            else
                spaceOn = true;
            spaceHandlerInd = true;
            mainFrame.getOptionPanel().setVisible(spaceOn);
            repaint();
        }
        if(ke.getKeyCode() == KeyEvent.VK_DELETE){
            for(int i=0; i < arrLines.size();i++){
                if(arrLines.get(i).isSelect()){
                    arrLines.remove(i);
                    Line.setArrSize(Line.getArrSize()-1);
                    i--;
                }
            }
            repaint();
            mainFrame.setAccessToOptionPanel(false);
        }

        if (ke.getKeyCode() == KeyEvent.VK_RIGHT && endArrIndex != chart.getArrBars().size() - 1) {
            moveRightChart(1);
            repaint();
        } else {
            if (ke.getKeyCode() == KeyEvent.VK_LEFT && startArrIndex != 0) {
                moveLeftChart(1);
                repaint();
            }
        }
    }
    @Override
    public void keyReleased(KeyEvent ke) {
        if (ke.getKeyCode() == KeyEvent.VK_CONTROL) {
            ctrlOn = false;
            firstPointOn = false;
            secondPointOn = false;
            repaint();
        }
    }
    //</editor-fold>
}