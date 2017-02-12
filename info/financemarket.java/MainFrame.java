package financemarket;

import java.io.IOException;
import javax.swing.*;
import java.awt.*;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.ComponentEvent;
import java.awt.event.ComponentListener;
import java.awt.event.FocusEvent;
import java.awt.event.FocusListener;
import java.awt.event.ItemEvent;
import java.awt.event.ItemListener;
import java.awt.event.KeyListener;
import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;
import java.awt.event.MouseMotionListener;
import java.awt.event.TextEvent;
import java.awt.event.TextListener;
import java.util.ArrayList;
import javax.swing.border.EtchedBorder;
import javax.swing.border.TitledBorder;
import javax.swing.event.CaretEvent;
import javax.swing.event.CaretListener;
import javax.swing.event.ChangeListener;

/**
 *  Главный класс
 * @author Tyshkovets Nick
 * @version 1.0
 */
public class MainFrame extends JFrame  implements FocusListener,  MouseMotionListener, TextListener, ActionListener, MouseListener, ComponentListener{  
    private JToolBar toolBar;
    private ChartPanel chartPanel;
    private JPanel optionPanel;
    private JTextField tName;
    private JTextField tMinCountPips;
    private JTextField tMaxCountPips;
    private JTextField tMinCountBars;
    private JTextField tMaxCountBars;
    private JTextField tFather;
    private JTextField tMinPipsRatio;
    private JTextField tMaxPipsRatio;
    private JTextField tMinBarsRatio;
    private JTextField tMaxBarsRatio;
    private JTextField tPipsUnionCount;
    private JTextField tBarsUnionCount;
    private JTextField tMinPeriod;
    private JTextField tMaxPeriod;
    
    
    private JCheckBox chbIsStrongLine;
    private JCheckBox chbIsForeignLine;
    private JCheckBox chbIsPipsRatio;
    private JCheckBox chbIsBarsRatio;
    
    private JToolBar timeFrame;
    private JToolBar timeFrameInOP;
    private ButtonGroup bgroup;
    private Font font;
    
    private ArrayList<JLabel> arrJLabel;
    private ArrayList<JTextField> arrJText;
    private int widthOptionPanel;
    
    MainFrame() throws IOException {
        super("Графическое приложение");
        this.setBackground(Color.WHITE);
        setBounds(50,50,1000, 450);
        this.setVisible(true);
        this.setVisible(false);
        
        arrJLabel = new ArrayList<JLabel>();
        arrJText = new ArrayList<JTextField>();
        
        int fontSize = 10;
        font = new Font("",Font.PLAIN,fontSize);
        
        addMenuBar(new MenuBar());
        addToolBar();
        
        widthOptionPanel = 200;
        addOptionPanel( widthOptionPanel );
        
//        setAccessToOptionPanel(false);
        chartPanel = new ChartPanel(this.getContentPane().getWidth() , this.getContentPane().getHeight(), this);
        add(chartPanel);
        chartPanel.requestFocus();
        
//        optionPanel.setVisible(false);

        this.setVisible(true);
        setDefaultCloseOperation(EXIT_ON_CLOSE);
    }
    
    private void addMenuBar(MenuBar mb){
        setMenuBar(mb);
        Menu f=new Menu("Файл");
        Menu v=new Menu("Вид");
        mb.add(f);
        mb.add(v);
    }
    private void addToolBar(){
        toolBar = new JToolBar( JToolBar.HORIZONTAL);
        toolBar.setFloatable(false);
        
        timeFrame = new JToolBar( JToolBar.HORIZONTAL);
        timeFrame.setFloatable(false);
        
        int fontSize = 10;
        Font font = new Font("",Font.PLAIN,fontSize);
        
        bgroup = new ButtonGroup();
        
        JToggleButton m1 = new JToggleButton("M1");
        JToggleButton m5 = new JToggleButton("M5");
        JToggleButton m15 = new JToggleButton("M15");
        JToggleButton m30 = new JToggleButton("M30");
        JToggleButton h1 = new JToggleButton("H1");
        JToggleButton h4 = new JToggleButton("H4");
        JToggleButton d1 = new JToggleButton("D1");
        JToggleButton w1 = new JToggleButton("W1");
        JToggleButton mn = new JToggleButton("MN");
        
        m1.setActionCommand("M1");
        m5.setActionCommand("M5");
        m15.setActionCommand("M15");
        m30.setActionCommand("M30");
        h1.setActionCommand("H1");
        h4.setActionCommand("H4");
        d1.setActionCommand("D1");
        w1.setActionCommand("W1");
        mn.setActionCommand("MN");
        
        timeFrame.add( m1 );
        timeFrame.add( m5 );
        timeFrame.add( m15 );
        timeFrame.add( m30 );
        timeFrame.add( h1 );
        timeFrame.add( h4 );
        timeFrame.add( d1 );
        timeFrame.add( w1 );
        timeFrame.add( mn );
        
        
//        timeFrameInOP = new JPanel( );
//        timeFrameInOP.add( new JToggleButton("кнопка") );
//        timeFrameInOP.add( m1 );
//        timeFrameInOP.add( m5 );
//        timeFrameInOP.add( m15 );
//        timeFrameInOP.add( m30 );
//        timeFrameInOP.add( h1 );
//        timeFrameInOP.add( h4 );
//        timeFrameInOP.add( d1 );
//        timeFrameInOP.add( w1 );
//        timeFrameInOP.add( mn );
//        for( int i=0; i < timeFrameInOP.getComponentCount();i++){
//            timeFrameInOP.getComponent(i).setFont(font);
//        }
//        timeFrameInOP.setFloatable(false);
        
        m1.setSelected(true);
        
        for( int i=0; i < timeFrame.getComponentCount();i++){
            timeFrame.getComponent(i).setFont(font);
            ((JToggleButton)(timeFrame.getComponent(i))).setFocusPainted(false);
            ((JToggleButton)(timeFrame.getComponent(i))).addActionListener(this);
            bgroup.add((JToggleButton)timeFrame.getComponent(i));
        }
        
        toolBar.add( timeFrame );
        toolBar.add( new JCheckBox() );
        add( toolBar, BorderLayout.NORTH);
    }
    
    private void createTimeFrameInOP(){
        JToggleButton m1 = new JToggleButton("M1");
        JToggleButton m5 = new JToggleButton("M5");
        JToggleButton m15 = new JToggleButton("M15");
        JToggleButton m30 = new JToggleButton("M30");
        JToggleButton h1 = new JToggleButton("H1");
        JToggleButton h4 = new JToggleButton("H4");
        JToggleButton d1 = new JToggleButton("D1");
        JToggleButton w1 = new JToggleButton("W1");
        JToggleButton mn = new JToggleButton("MN");
        
        m1.setActionCommand("M1");
        m5.setActionCommand("M5");
        m15.setActionCommand("M15");
        m30.setActionCommand("M30");
        h1.setActionCommand("H1");
        h4.setActionCommand("H4");
        d1.setActionCommand("D1");
        w1.setActionCommand("W1");
        mn.setActionCommand("MN");
        
        timeFrameInOP.add( m1 );
        timeFrameInOP.add( m5 );
        timeFrameInOP.add( m15 );
        timeFrameInOP.add( m30 );
        timeFrameInOP.add( h1 );
        timeFrameInOP.add( h4 );
        timeFrameInOP.add( d1 );
        timeFrameInOP.add( w1 );
        timeFrameInOP.add( mn );
        
        for(int i=0; i<timeFrameInOP.getComponentCount();i++){
            (timeFrameInOP.getComponent(i)).setFont(font);
        }
    }
    private void addElementToOP(JComponent comp, Container cont){
        JPanel panel = new JPanel(new FlowLayout(FlowLayout.LEFT));
        panel.add(comp);
        cont.add(panel);
        comp.setFont(font);
    }
    private void addOptionPanel(int widthOptionPanel){
        optionPanel = new JPanel();
        timeFrameInOP = new JToolBar( JToolBar.HORIZONTAL);
        timeFrameInOP.setFloatable(false);
        
        JScrollPane jscrl = new JScrollPane(optionPanel);
        jscrl.setPreferredSize(new Dimension(widthOptionPanel, 100));
        jscrl.getVerticalScrollBar().setUnitIncrement(10);
        jscrl.setHorizontalScrollBarPolicy(JScrollPane.HORIZONTAL_SCROLLBAR_NEVER);
        BoxLayout bxLayout = new BoxLayout(optionPanel, BoxLayout.Y_AXIS);
        optionPanel.setLayout( bxLayout );
        this.add(jscrl, BorderLayout.EAST);
        
        JPanel jpTimeFrames = new JPanel( new FlowLayout(FlowLayout.LEFT) );
        jpTimeFrames.setLayout(new BoxLayout(jpTimeFrames, BoxLayout.Y_AXIS));
        JPanel jpName = new JPanel( new FlowLayout(FlowLayout.LEFT) );
        JPanel jpIsStrongLine = new JPanel( new FlowLayout(FlowLayout.LEFT) );
        JPanel jpIsForeignLine = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        JPanel jpCountPips = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        JPanel jpCountBars = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        JPanel jpFather = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        JPanel jpPipsRatio = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        jpPipsRatio.setLayout(new BoxLayout(jpPipsRatio, BoxLayout.Y_AXIS));
        JPanel jpPipsRatioChild = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        JPanel jpBarsRatio = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        jpBarsRatio.setLayout(new BoxLayout(jpBarsRatio, BoxLayout.Y_AXIS));
        JPanel jpBarsRatioChild = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        JPanel jpPipsUnionCount = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        JPanel jpBarsUnionCount = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        JPanel jpPeriod = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        jpPeriod.setLayout(new BoxLayout(jpPeriod, BoxLayout.Y_AXIS));
        JPanel jpPeriodChild = new JPanel( new FlowLayout(FlowLayout.LEFT)  );
        
        JPanel jpMainName = new JPanel();
        JPanel jpMainFather = new JPanel();
        JPanel jpMainUnion = new JPanel();
        jpMainName.setLayout(new BoxLayout(jpMainName, BoxLayout.Y_AXIS));
        jpMainFather.setLayout(new BoxLayout(jpMainFather, BoxLayout.Y_AXIS));
        jpMainUnion.setLayout(new BoxLayout(jpMainUnion, BoxLayout.Y_AXIS));
        
        createTimeFrameInOP();
       
        JLabel lTimeFrames = new JLabel("Используемые таймфреймы:");
        JLabel lName = new JLabel("Имя:");
        JLabel lMinCountPips = new JLabel("<html>Мин. к-во<p>пунктов:</html>");
        JLabel lMaxCountPips = new JLabel("<html>Макс. к-во<p>пунктов:</html>");
        JLabel lMinCountBars = new JLabel("<html>Мин. <p>к-во бар:</html>");
        JLabel lMaxCountBars = new JLabel("<html>Макс. <p>к-во бар:</html>");
        JLabel lFather = new JLabel("Родитель:");
        JLabel lPipsRatio = new JLabel("Зависимость от к-ва пунктов:");
        JLabel lBarsRatio = new JLabel("Зависимость от к-ва свечек:");
        JLabel lmin = new JLabel("Мин.:");
        JLabel lmax = new JLabel("Макс.:");
        JLabel lmin2 = new JLabel("Мин.:");
        JLabel lmax2 = new JLabel("Макс.:");
        JLabel lPipsUnionCount = new JLabel("К-во пунктов для объедин-я:");
        JLabel lBarsUnionCount = new JLabel("К-во свечек для  объедин-я:");
        JLabel lPeriod = new JLabel("Временной диапазон для поиска:");
        JLabel lFrom = new JLabel("с:");
        JLabel lUntil = new JLabel("до:");
        
        arrJLabel.add(lTimeFrames); arrJLabel.add(lName); arrJLabel.add(lMinCountPips); arrJLabel.add(lMaxCountPips); arrJLabel.add(lMinCountBars);
        arrJLabel.add(lMaxCountBars); arrJLabel.add(lFather); arrJLabel.add(lPipsRatio); arrJLabel.add(lBarsRatio); arrJLabel.add(lmin);
        arrJLabel.add(lmax); arrJLabel.add(lmin2); arrJLabel.add(lmax2); arrJLabel.add(lPipsUnionCount); arrJLabel.add(lBarsUnionCount);
        arrJLabel.add(lPeriod); arrJLabel.add(lFrom); arrJLabel.add(lUntil);
        
        tName = new JTextField("",8);
        tMinCountPips = new JTextField("",2);
        tMaxCountPips = new JTextField("",2);
        tMinCountBars = new JTextField("",2);
        tMaxCountBars = new JTextField("",2);
        tFather = new JTextField("",8);
        tMinPipsRatio = new JTextField("",2);
        tMaxPipsRatio = new JTextField("",2);
        tMinBarsRatio = new JTextField("",2);
        tMaxBarsRatio = new JTextField("",2);
        tPipsUnionCount = new JTextField("",2);
        tBarsUnionCount = new JTextField("",2);
        tMinPeriod = new JTextField("",5);
        tMaxPeriod = new JTextField("",5);
        
        arrJText.add(tName); arrJText.add(tMinCountPips); arrJText.add(tMaxCountPips); arrJText.add(tMinCountBars); arrJText.add(tMaxCountBars); 
        arrJText.add(tFather); arrJText.add(tMinPipsRatio); arrJText.add(tMaxPipsRatio); arrJText.add(tMinBarsRatio); arrJText.add(tMaxBarsRatio); 
        arrJText.add(tPipsUnionCount); arrJText.add(tBarsUnionCount); arrJText.add(tMinPeriod); arrJText.add(tMaxPeriod);
        
        chbIsStrongLine = new JCheckBox("Линия строгая?");
        chbIsForeignLine = new JCheckBox("Линия зависима?");
        chbIsPipsRatio = new JCheckBox();
        chbIsBarsRatio = new JCheckBox();
        /*************************************************************************************************************************************/
        /*************************************************************************************************************************************/     
        addElementToOP(lTimeFrames,jpTimeFrames );
        JScrollPane scrlTF = new JScrollPane(timeFrameInOP);
        addElementToOP(scrlTF,jpTimeFrames );
        scrlTF.setPreferredSize(new Dimension(widthOptionPanel-30, 50));
        
        jpName.add(lName);
        jpName.add(tName);
        jpIsStrongLine.add(chbIsStrongLine);
        jpIsForeignLine.add(chbIsForeignLine);
        jpCountPips.add(lMinCountPips);
        jpCountPips.add(tMinCountPips);
        jpCountPips.add(lMaxCountPips);
        jpCountPips.add(tMaxCountPips);
        jpCountBars.add(lMinCountBars);
        jpCountBars.add(tMinCountBars);
        jpCountBars.add(lMaxCountBars);
        jpCountBars.add(tMaxCountBars);
        
        jpFather.add(lFather);
        jpFather.add(tFather);
        addElementToOP(lPipsRatio,jpPipsRatio );
        
        jpPipsRatioChild.add(this.chbIsPipsRatio);
        jpPipsRatioChild.add(lmin);
        jpPipsRatioChild.add(tMinPipsRatio);
        jpPipsRatioChild.add(lmax);
        jpPipsRatioChild.add(tMaxPipsRatio);
        jpPipsRatio.add(jpPipsRatioChild);
        
        addElementToOP(lBarsRatio,jpBarsRatio );
        jpBarsRatioChild.add(this.chbIsBarsRatio);
        jpBarsRatioChild.add(lmin2);
        jpBarsRatioChild.add(tMinBarsRatio);
        jpBarsRatioChild.add(lmax2);
        jpBarsRatioChild.add(tMaxBarsRatio);
        jpBarsRatio.add(jpBarsRatioChild);
        
        jpPipsUnionCount.add(lPipsUnionCount);
        jpPipsUnionCount.add(tPipsUnionCount);
        jpBarsUnionCount.add(lBarsUnionCount);
        jpBarsUnionCount.add(tBarsUnionCount);
        
        addElementToOP(lPeriod,jpPeriod );
        jpPeriodChild.add(lFrom);
        jpPeriodChild.add(tMinPeriod);
        jpPeriodChild.add(lUntil);
        jpPeriodChild.add(tMaxPeriod);
        jpPeriod.add(jpPeriodChild);
        
        jpMainName.add(jpName);
        jpMainName.add(jpIsStrongLine);
        jpMainName.add(jpIsForeignLine);
        jpMainName.add(jpCountPips);
        jpMainName.add(jpCountBars);
        jpMainFather.add(jpFather);
        jpMainFather.add(jpPipsRatio);
        jpMainFather.add(jpBarsRatio);
        jpMainUnion.add(jpPipsUnionCount);
        jpMainUnion.add(jpBarsUnionCount);
        
        optionPanel.add(jpTimeFrames);
        optionPanel.add(jpMainName);
        optionPanel.add(jpMainFather);
        optionPanel.add(jpMainUnion);
        optionPanel.add(jpPeriod);
        
        for(int i=0;i<optionPanel.getComponentCount();i++){
            ((JPanel)optionPanel.getComponent(i)).setBorder(new EtchedBorder());
        }
        
        lName.setFont(font); lMinCountPips.setFont(font); lMaxCountPips.setFont(font); lMinCountBars.setFont(font); lMaxCountBars.setFont(font);
        lFather.setFont(font); lPipsRatio.setFont(font); lBarsRatio.setFont(font); lmin.setFont(font); lmax.setFont(font); 
        lmin2.setFont(font); lmax2.setFont(font); lPipsUnionCount.setFont(font); lBarsUnionCount.setFont(font); lPeriod.setFont(font); 
        lFrom.setFont(font); lUntil.setFont(font);
        chbIsStrongLine.setFont(font); chbIsForeignLine.setFont(font);
        addListeners();
    }
    
    private void addListeners(){
        
        tName.addFocusListener(this);
        tMinCountPips.addFocusListener(this);
        tMaxCountPips.addFocusListener(this);
        tMinCountBars.addFocusListener(this);
        tMaxCountBars.addFocusListener(this);
        tFather.addFocusListener(this);
        tMinPipsRatio.addFocusListener(this);
        tMaxPipsRatio.addFocusListener(this);
        tMinBarsRatio.addFocusListener(this);
        tMaxBarsRatio.addFocusListener(this);
        tPipsUnionCount.addFocusListener(this);
        tBarsUnionCount.addFocusListener(this);
        tMinPeriod.addFocusListener(this);
        tMaxPeriod.addFocusListener(this);
        
        tName.addActionListener(this);
        tMinCountPips.addActionListener(this);
        tMaxCountPips.addActionListener(this);
        tMinCountBars.addActionListener(this);
        tMaxCountBars.addActionListener(this);
        tFather.addActionListener(this);
        tMinPipsRatio.addActionListener(this);
        tMaxPipsRatio.addActionListener(this);
        tMinBarsRatio.addActionListener(this);
        tMaxBarsRatio.addActionListener(this);
        tPipsUnionCount.addActionListener(this);
        tBarsUnionCount.addActionListener(this);
        tMinPeriod.addActionListener(this);
        tMaxPeriod.addActionListener(this);
        
        this.chbIsStrongLine.addItemListener( new ItemListener(){
            @Override
            public void itemStateChanged(ItemEvent ie){
                if( chartPanel.isOneSelectedLine()){
                    if( chbIsStrongLine.isSelected())
                        chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setStrongLine(true);
                    else
                        chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setStrongLine(false);
                }
            }
        });
        this.chbIsForeignLine.addItemListener( new ItemListener(){
            @Override
            public void itemStateChanged(ItemEvent ie){
                setAccessToForeignLine();
            }
        });
        chbIsPipsRatio.addItemListener( new ItemListener(){
            @Override
            public void itemStateChanged(ItemEvent ie){
            }
        });
        chbIsBarsRatio.addItemListener( new ItemListener(){
            @Override
            public void itemStateChanged(ItemEvent ie){
            }
        });
//        this.chbImportantPips.addItemListener( new ItemListener(){
//            @Override
//            public void itemStateChanged(ItemEvent ie){
//                if( chartPanel.isOneSelectedLine()){
//                    if( chbImportantPips.isSelected()){
//                        System.out.println("Zashel");
//                        tMinCountPips.setEnabled(true);
//                        tMaxCountPips.setEnabled(true);
//                        chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setImportantPips(true);
//                    }
//                    else{
//                        chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setImportantPips(false);
//                        tMinCountPips.setEnabled(false);
//                        tMaxCountPips.setEnabled(false);
//                    }
//                }
//            }
//        });
        optionPanel.addFocusListener(this);
    }
    private float convertStringToFloat(String str, float prevValue){
        float res;
        try{
           res = Float.parseFloat(str);
        }catch(NumberFormatException nfe){
            return prevValue;
        }
        return res;
    }
    private int convertStringToInt(String str, int prevValue){
        int res;
        try{
           res = Integer.parseInt(str);
//           System.out.println("convertStringToInt: "+res);
        }catch(NumberFormatException nfe){
//            System.out.println("ATTANTIONN");
            return prevValue;
        }
        return res;
    }
    private void writeElement(){
        if( chartPanel.isOneSelectedLine()){
            chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setName(chartPanel.getArrLines(), tName.getText());
            tName.setText(chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getName() );

            chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setForeignName( chartPanel.getArrLines(), tFather.getText());
            tFather.setText(chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getForeignName() );
            
            /** Deviation*/
//            chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setDeviation( 
//                    convertStringToFloat(tDeviation.getText(), chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getDeviation() ));
//            tDeviation.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getDeviation() ) );
            
            /**MinMaxCountBars*/
            if( String.valueOf(chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountBar()).equals(tMaxCountBars.getText()) ){
                chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setMinCountBars( 
                        convertStringToInt(tMinCountBars.getText(), chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountBar()) );
                tMinCountBars.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountBar() ) );
                tMaxCountBars.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountBar() ) );
            }
            else
            if( String.valueOf(chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountBar()).equals(tMinCountBars.getText()) ){
                chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setMaxCountBars( 
                        convertStringToInt(tMaxCountBars.getText(), chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountBar()) );
                tMaxCountBars.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountBar() ) );
                tMinCountBars.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountBar() ) );
            }
            
            /**MinMaxCountPips*/
//                System.out.println("Starting...");
//                System.out.println("Staarting: min = "+chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountPips()
//                        +"; max = "+chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountPips());
            if( String.valueOf(chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountPips()).equals(tMaxCountPips.getText()) ){
                chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setMinCountPips( 
                        convertStringToInt(tMinCountPips.getText(), chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountPips()) );
                tMinCountPips.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountPips() ) );
                tMaxCountPips.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountPips() ) );
            }
            else
            if( String.valueOf(chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountPips()).equals(tMinCountPips.getText()) ){
                chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setMaxCountPips( 
                        convertStringToInt(tMaxCountPips.getText(), chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountPips()) );
                tMaxCountPips.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountPips() ) );
                tMinCountPips.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountPips() ) );
            }
            
            /** Ratio*/
//            chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setNumRatio( 
//                    convertStringToFloat(tRatio.getText(), chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getNumRatio() ) );
//            tRatio.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getNumRatio() ) );
//            System.out.println("newRatio = "+chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getNumRatio());            
//            System.out.println("deviation = "+chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getDeviation());
        }
        setAccessToForeignLine();
    }
    private void displayNewChart(String path){
        chartPanel.getChart().readChart( path );
        chartPanel.calculateStartEndArrIndex();
        chartPanel.calculateOptionDateField();
        chartPanel.repaint();
    }
    private void displayTimeChartAfterChoose(ActionEvent ae){
        switch(ae.getActionCommand()){
            case "M1": displayNewChart(chartPanel.getDefaultPath()+"\\EURUSD\\EURUSD_M1_2014-09-03_2016-07-01.txt" );
                break;
            case "M5": displayNewChart(chartPanel.getDefaultPath()+"\\EURUSD\\EURUSD_M5_2013-09-05_2016-08-13.txt" );
                break;
            case "M15": displayNewChart(chartPanel.getDefaultPath()+"\\EURUSD\\EURUSD_M15_2013-02-08_2016-08-13.txt" );
            break;
            case "M30": displayNewChart(chartPanel.getDefaultPath()+"\\EURUSD\\EURUSD_M30_2011-08-10_2016-08-13.txt" );
            break;
            case "H1": displayNewChart(chartPanel.getDefaultPath()+"\\EURUSD\\EURUSD_H1_2010-01-06_2016-08-13.txt" );
            break;
            case "H4": displayNewChart(chartPanel.getDefaultPath()+"\\EURUSD\\EURUSD_H4_2007-09-06_2016-08-13.txt" );
            break;
            case "D1": displayNewChart(chartPanel.getDefaultPath()+"\\EURUSD\\EURUSD_D1_2000-01-05_2016-08-13.txt" );
            break;
            case "W1": displayNewChart(chartPanel.getDefaultPath()+"\\EURUSD\\EURUSD_W1_1993-09-16_2016-08-13.txt" );
            break;
            case "MN": displayNewChart(chartPanel.getDefaultPath()+"\\EURUSD\\EURUSD_MN_1995-02-01_2016-06-01.txt" );
            break;
        }
        chartPanel.setNewCoursor(false);
        
    }
    
    public void setAccessToOptionPanel(boolean access){
        if(!access){            
            tName.setText("");
            tMinCountPips.setText("");
            tMaxCountPips.setText("");
            tMinCountBars.setText("");
            tMaxCountBars.setText("");
            tFather.setText("");
            tMinPipsRatio.setText("");
            tMaxPipsRatio.setText("");
            tMinBarsRatio.setText("");
            tMaxBarsRatio.setText("");
            tPipsUnionCount.setText("");
            tBarsUnionCount.setText("");
            this.tMinPeriod.setText("");
            this.tMaxPeriod.setText("");
            chbIsStrongLine.setSelected(access);
            chbIsForeignLine.setSelected(access);
            chbIsPipsRatio.setSelected(access);
            chbIsBarsRatio.setSelected(access);
        }
        access=false;
        System.out.println("access = "+access+"; chbIsStrongLine = "+chbIsStrongLine.isEnabled()); 
        for(int i=0;i< this.timeFrameInOP.getComponentCount();i++)
            this.timeFrameInOP.getComponent(i).setEnabled(access);
        for(int i=0;i<arrJLabel.size();i++)
            arrJLabel.get(i).setEnabled(access);
        for(int i=0;i<arrJText.size();i++)
            arrJText.get(i).setEnabled(access);
        chbIsStrongLine.setEnabled(access);
        chbIsForeignLine.setEnabled(access);
        chbIsPipsRatio.setEnabled(access);
        chbIsBarsRatio.setEnabled(access);
                
    }
    public void setAccessToForeignLine(){
        if( chartPanel.isOneSelectedLine()){
            if( chbIsForeignLine.isSelected()){
                tFather.setEnabled(true);
                chbIsPipsRatio.setEnabled(true);
                this.tMinPipsRatio.setEnabled(true);
                this.tMaxPipsRatio.setEnabled(true);
                chbIsBarsRatio.setEnabled(true);
                this.tMinBarsRatio.setEnabled(true);
                this.tMaxBarsRatio.setEnabled(true);
                
                this.tMinCountPips.setEnabled(false);
                this.tMaxCountPips.setEnabled(false);
                this.tMinCountBars.setEnabled(false);
                this.tMaxCountBars.setEnabled(false);
            }
            else{
                tFather.setEnabled(false);
                chbIsPipsRatio.setEnabled(false);
                this.tMinPipsRatio.setEnabled(false);
                this.tMaxPipsRatio.setEnabled(false);
                chbIsBarsRatio.setEnabled(false);
                this.tMinBarsRatio.setEnabled(false);
                this.tMaxBarsRatio.setEnabled(false);
                
                this.tMinCountPips.setEnabled(true);
                this.tMaxCountPips.setEnabled(true);
                this.tMinCountBars.setEnabled(true);
                this.tMaxCountBars.setEnabled(true);
                
                chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).setForeignName(chartPanel.getArrLines(), "");
                tFather.setText("");
                tMinPipsRatio.setText("");
                tMaxPipsRatio.setText("");
                tMinBarsRatio.setText("");
                tMaxBarsRatio.setText("");
            }
        }
    }  
    @Override
    public void focusGained(FocusEvent e) {
        if( chartPanel.isOneSelectedLine()){
            tName.setText(chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getName() );
            tMinCountBars.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountBar() ) );
            tMaxCountBars.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountBar() ) );
//            tDeviation.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getDeviation() ) );
            tMinCountPips.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMinCountPips() ) );
            tMaxCountPips.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getMaxCountPips() ) );
            tFather.setText(chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getForeignName() );
//            tRatio.setText( String.valueOf( chartPanel.getArrLines().get( chartPanel.getSelectLineIndex() ).getNumRatio() ) );
        }
    }
    @Override
    public void focusLost(FocusEvent e) {
       writeElement();
    }
    
    @Override
    public void actionPerformed(ActionEvent ae) {
        writeElement();
        displayTimeChartAfterChoose(ae);
    }

    @Override
    public void mouseClicked(MouseEvent e) {    }
    @Override
    public void mousePressed(MouseEvent e) {
            System.out.println("mishka");
        if (e.getModifiers() == MouseEvent.BUTTON1_MASK) {
            System.out.println("mishka");
        }
    }
    @Override
    public void mouseReleased(MouseEvent e) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }
    @Override
    public void mouseEntered(MouseEvent e) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }
    @Override
    public void mouseExited(MouseEvent e) {
    }
    @Override
    public void mouseDragged(MouseEvent e) {    
    }
    @Override
    public void mouseMoved(MouseEvent e) {
        for(int i=0;i<this.getComponentCount();i++)
            System.out.println("this.getComponentCount() = "+this.getComponentCount());
        e.getPoint();
    }
/**
     * @param args the command line arguments
     */
    public static void main(String[] args) throws IOException {
         new MainFrame();
    }

    @Override
    public void textValueChanged(TextEvent e) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    public JPanel getOptionPanel() {
//    public JScrollPane getOptionPanel() {
        return optionPanel;
    }
    public JTextField gettName() {
        return tName;
    }
    public JTextField gettMinCountBars() {
        return tMinCountBars;
    }
    public JTextField gettMaxCountBars() {
        return tMaxCountBars;
    }
    public JCheckBox isChbForeignLine() {
        return chbIsForeignLine;
    }
//    public JTextField gettDeviation() {
//        return tDeviation;
//    }
    public JTextField gettMinCountPips() {
        return tMinCountPips;
    }
    public JTextField gettMaxCountPips() {
        return tMaxCountPips;
    }
    public JTextField gettFather() {
        return tFather;
    }
//    public JTextField gettRatio() {
//        return tRatio;
//    }
    public JCheckBox isChbStrongLine() {
        return chbIsStrongLine;
    }
//    public JCheckBox getChbImportantPips() {
//        return chbImportantPips;
//    }
    public ButtonGroup getBGroup(){
        return this.bgroup;
    }

    @Override
    public void componentResized(ComponentEvent e) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    @Override
    public void componentMoved(ComponentEvent e) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

    @Override
    public void componentShown(ComponentEvent e) {
        System.out.println("SHOWN");
    }

    @Override
    public void componentHidden(ComponentEvent e) {
        System.out.println("HIDDEN");
    }
}