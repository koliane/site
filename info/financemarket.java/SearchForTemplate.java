package financemarket;
import java.util.ArrayList;
/**
 * @author Tyshkovets Nick
 * @version 1.0
 */
public class SearchForTemplate {
    private ArrayList<Line> arrLines;
    private ArrayList<Bar> arrBars;
    private ArrayList<ArrayList> arrStartIndex;
    private ArrayList<ArrayList> arrCountBar;
    private ArrayList< ArrayList < LineOnChart > > arrComboLines;
    /** Колво бар, при котором берется один шаблон*/
    private int union;
        
    SearchForTemplate( ArrayList<Bar> arrBars, ArrayList<Line> arrLines){
        this.arrLines = arrLines;
        this.arrBars = arrBars;
        
        arrStartIndex = new ArrayList<ArrayList>();
        arrCountBar = new ArrayList<ArrayList>();
        
        arrComboLines = new ArrayList<ArrayList<LineOnChart>>();
    }
    
    public ArrayList<ArrayList> getArrStartIndex(){
        return this.arrStartIndex;
    }
    public ArrayList<ArrayList> getArrCountBar(){
        return this.arrCountBar;
    }
    public ArrayList<ArrayList<LineOnChart>> getArrComboLines(){
        return this.arrComboLines;
    }
    
    private float returnMinPriceInLine(int startArrBarsIndex,  int countBar){
        float minPriceInLine = arrBars.get(startArrBarsIndex).getPriceL();
        if(countBar > 1)
            for(int i = 1; i < countBar; i++){
                if(arrBars.get(startArrBarsIndex+i).getPriceL() < minPriceInLine)
                    minPriceInLine = arrBars.get(startArrBarsIndex+i).getPriceL();
            }
        return minPriceInLine;
    }
    private float returnMaxPriceInLine(int startArrBarsIndex,  int countBar){
        float maxPriceInLine = arrBars.get(startArrBarsIndex).getPriceH();
        if(countBar > 1)
            for(int i = 1; i < countBar; i++){
                if(arrBars.get(startArrBarsIndex+i).getPriceH() > maxPriceInLine)
                    maxPriceInLine = arrBars.get(startArrBarsIndex+i).getPriceH();
            }
        return maxPriceInLine;
    }
    
    private void unionComboLines(){
        union = 3;
        ArrayList arrDel = new ArrayList();
        for(int i=0; i < arrComboLines.size();i++)
            for(int j=0; j < arrComboLines.size();j++){
                if( i!= j && arrComboLines.get(i).size() == arrComboLines.get(j).size() ){
                    int k;
                    for( k=0; k < arrComboLines.get(i).size(); k++){
                        /**Если разница между соответствующими концами линий различаются менее чем на union то суммируем k*/
                        if( Math.abs( arrComboLines.get(i).get(k).getStartBar() - arrComboLines.get(j).get(k).getStartBar() ) <= union
                                && Math.abs( arrComboLines.get(i).get(k).getStartBar() + arrComboLines.get(i).get(k).getCountBar()
                                        - (arrComboLines.get(j).get(k).getStartBar() + arrComboLines.get(j).get(k).getCountBar()) ) <= union) {
                            /** Если у первых линий есть совпадающие точки**/
                            if( arrComboLines.get(i).get(k).getStartBar() >= arrComboLines.get(j).get(k).getStartBar() 
                                    && arrComboLines.get(i).get(k).getStartBar() < arrComboLines.get(j).get(k).getStartBar() + arrComboLines.get(j).get(k).getCountBar()
                                    || arrComboLines.get(i).get(k).getStartBar() +  arrComboLines.get(i).get(k).getCountBar() > arrComboLines.get(j).get(k).getStartBar() 
                                    && arrComboLines.get(i).get(k).getStartBar() +  arrComboLines.get(i).get(k).getCountBar() <= arrComboLines.get(j).get(k).getStartBar() + arrComboLines.get(j).get(k).getCountBar())
                            continue;
                            else
                                break;
                        }
                        else 
                            break;
                    }
                    /**Если все линии находятся рядом (в пределах union), то добавляем индекс соответствующей комбинации (которая меньше в барах) в массив индексов, которые потом будут удаляться*/
                    if( k == arrComboLines.get(i).size()){
                        if( arrComboLines.get(i).get( arrComboLines.get(i).size() -1 ).getStartBar() +  arrComboLines.get(i).get( arrComboLines.get(i).size() -1 ).getCountBar()
                                - arrComboLines.get(i).get( 0 ).getStartBar() == arrComboLines.get(j).get( arrComboLines.get(j).size() -1 ).getStartBar() +  arrComboLines.get(j).get( arrComboLines.get(j).size() -1 ).getCountBar()
                                - arrComboLines.get(j).get( 0 ).getStartBar() ){
                            if( !arrDel.contains(i) && !arrDel.contains(j) )
                                arrDel.add(i);
                        }
                        else
                        if( arrComboLines.get(i).get( arrComboLines.get(i).size() -1 ).getStartBar() +  arrComboLines.get(i).get( arrComboLines.get(i).size() -1 ).getCountBar()
                                - arrComboLines.get(i).get( 0 ).getStartBar() < arrComboLines.get(j).get( arrComboLines.get(j).size() -1 ).getStartBar() +  arrComboLines.get(j).get( arrComboLines.get(j).size() -1 ).getCountBar()
                                - arrComboLines.get(j).get( 0 ).getStartBar()){
                            arrDel.add(j);
                        }else
                            arrDel.add(i);
                    }
                }
            }
        /**Сортировка по убыванию*/
        int temp;
        for(int i=0; i<arrDel.size()-1;i++)
            for(int j=i+1; j<arrDel.size();j++){
                if( (int)arrDel.get(i) < (int)arrDel.get(j) ){
                    temp = (int)arrDel.get(i);
                    arrDel.set(i, arrDel.get(j));
                    arrDel.set(j, temp);
                }
            }
        
        for(int i=0; i<arrDel.size();i++){
            arrComboLines.remove((int)arrDel.get(i));
        }  
//        System.out.println("arrDel.size() = "+arrDel.size());
    }
    
    private boolean findLine(int startBarIndex, int countBars, Line line){
        boolean findStrongLine = false;
        boolean findImportantPipsLine = false;
        float tempMinPriceInLine = arrBars.get(startBarIndex).getPriceL();
        float tempMaxPriceInLine = arrBars.get(startBarIndex).getPriceH();
        
        float minPriceInLine = returnMinPriceInLine(startBarIndex, countBars);
        float maxPriceInLine = returnMaxPriceInLine(startBarIndex, countBars);
        
        if(line.isStrongLine()){
            if( line.isBuyLine() && tempMinPriceInLine == minPriceInLine && arrBars.get(startBarIndex+countBars-1).getPriceH() == maxPriceInLine
                    || line.isSellLine() && tempMaxPriceInLine == maxPriceInLine  && arrBars.get(startBarIndex+countBars-1).getPriceL() == minPriceInLine)
                findStrongLine = true;
        }
        else
        {
            if( line.isBuyLine() && tempMinPriceInLine == minPriceInLine 
                    || line.isSellLine() && tempMaxPriceInLine == maxPriceInLine )
                findStrongLine = true;
        }    
        
        if(line.isImportantPips()){
            if( Math.round((maxPriceInLine - minPriceInLine)/ChartPanel.onePips ) >= line.getMinCountPips() 
                    && Math.round( (maxPriceInLine - minPriceInLine)/ChartPanel.onePips ) <= line.getMaxCountPips() )
                findImportantPipsLine = true;
        }
        else
            findImportantPipsLine = true;
        
        return (findStrongLine && findImportantPipsLine );
    }
    private void findNeighbour( ArrayList arrStart1, ArrayList arrCount1, ArrayList arrStart2, ArrayList arrCount2, int index ){
        
        for(int i=0; i < arrStart1.size(); i++)
            for(int j=0; j < arrStart2.size(); j++){
//                if( arrLines.get(index-1).getMinCountBar() == 0){
//                    addNullLine(i);
//                }
                
                if( (int)arrStart1.get(i)+(int)arrCount1.get(i) == (int)arrStart2.get(j) )
                    correctlyAddIndexAndCount((int)arrStart1.get(i), (int)arrCount1.get(i),(int)arrStart2.get(j), (int)arrCount2.get(j), index);
            }
    }  
    public void findAllTemplates( final ArrayList<Line> arrLinesFullTemplate ){
        ArrayList arrIndexNull = new ArrayList();
        ArrayList arrDeleteIndex = new ArrayList();
        
        /**Вычисляем индексы с нулевыми линиями   */
        for(int i=0; i < arrLinesFullTemplate.size(); i++){
            if(arrLinesFullTemplate.get(i).getMinCountBar() == 0)
                arrIndexNull.add(i);
        }
        
        int dbinaryCodeOnNullArr;
        final int b = 1;
        /**Цикл комбинирует линии с нулевыми линиями*/
        for( int i=0; i < Math.pow( 2, arrIndexNull.size() ); i++ ){
            this.arrLines = (ArrayList<Line>) arrLinesFullTemplate.clone();
            this.arrStartIndex.clear();
            this.arrCountBar.clear();
        /**Вычислить arrDeleteIndex - содержит индексы, которые необходимо удалить из arrLines*/
            arrDeleteIndex= (ArrayList) arrIndexNull.clone();
            dbinaryCodeOnNullArr = i;
            for(int j=0; j < arrDeleteIndex.size(); j++){
                if( ( dbinaryCodeOnNullArr & b ) == 0 ){
                    arrDeleteIndex.remove( arrDeleteIndex.size() -j - 1 );
                    j--;
                }
                dbinaryCodeOnNullArr = dbinaryCodeOnNullArr >> 1;
            }
            /**Сортировка полученного массива по возрастанию*/
            int temp;
            for(int j=0; j < arrDeleteIndex.size()-1; j++)
                for(int k=j+1; k < arrDeleteIndex.size(); k++)   
                    if ( (int)arrDeleteIndex.get(j) > (int)arrDeleteIndex.get(k)) {
                        temp = (int)arrDeleteIndex.get(j);
                        arrDeleteIndex.set(j, (int)arrDeleteIndex.get(k));
                        arrDeleteIndex.set(k, temp);
                    }
        /******/
            /**Удаление соответствующих линий в arrLines (формирование другого шаблона)*/
            int removeCount = 0;
            for (int j = 0; j < arrDeleteIndex.size(); j++) {
                arrLines.remove( (int)arrDeleteIndex.get(j) - removeCount );
                removeCount++;
            }
            
            findTemplate();
        }
        System.out.println("arrComboLines.size() = "+arrComboLines.size());
        //printCombo();
        unionComboLines();
        
    }
        /**Метод для корректного добавления элемента в соответсвующую комбинацию линий.
     * @param numElemToAdd - номер линии в шаблоне*/    
    private void correctlyAddIndexAndCount( int prevStartInd, int prevCountBar, int addStartIndex, int addCountBar, int numElemToAdd ){
        int num = arrStartIndex.size();
        for( int i=0; i < num; i++ ){
            /***/
            if( (int) arrStartIndex.get(i).get( arrStartIndex.get(i).size()-1 ) == prevStartInd 
                    && (int)arrCountBar.get(i).get( arrCountBar.get(i).size()-1 ) == prevCountBar
                    && (int)arrStartIndex.get(i).get(arrStartIndex.get(i).size()-1) != addStartIndex
                    && (int)arrStartIndex.get(i).size() == numElemToAdd
                    ){
                arrStartIndex.get(i).add(addStartIndex);
                arrCountBar.get(i).add(addCountBar);
            }
            else
            if( arrStartIndex.get(i).size() >= 2
                    && (int)arrStartIndex.get(i).size() == numElemToAdd+1
                    && (int)arrStartIndex.get(i).get( arrStartIndex.get(i).size()-2 ) == prevStartInd
                    && (int)arrCountBar.get(i).get( arrStartIndex.get(i).size()-2 ) == prevCountBar
                    && !((int) arrStartIndex.get(i).get( arrStartIndex.get(i).size()-1 ) == addStartIndex
                        && (int) arrCountBar.get(i).get( arrStartIndex.get(i).size()-1 ) == addCountBar)
                    ){
//                System.out.println("startIndex = "+(int) arrStartIndex.get(i).get( arrStartIndex.get(i).size()-1 )
//                        +"; countBar = "+(int) arrCountBar.get(i).get( arrStartIndex.get(i).size()-1 )
//                        +"; addStartIndex = "+addStartIndex+"; addCountBar = "+addCountBar);
                arrStartIndex.add(new ArrayList());
                arrCountBar.add(new ArrayList());

                arrStartIndex.get(arrStartIndex.size()-1).addAll(arrStartIndex.get(i) );
                arrCountBar.get(arrCountBar.size()-1).addAll(arrCountBar.get(i) );

                arrStartIndex.get(arrStartIndex.size()-1).remove(arrStartIndex.get(arrStartIndex.size()-1).size()-1);
                arrCountBar.get(arrCountBar.size()-1).remove(arrCountBar.get(arrCountBar.size()-1).size()-1 );

                arrStartIndex.get(arrStartIndex.size()-1).add( addStartIndex );
                arrCountBar.get(arrCountBar.size()-1).add( addCountBar );
//                break;
            }
        }
    }
    private void collectRelevantLines(ArrayList<ArrayList> arrStartInd, ArrayList<ArrayList> arrCountBr){
        for(int i=0; i < arrStartInd.get(0).size(); i++){
            arrStartIndex.add(new ArrayList());
            arrCountBar.add(new ArrayList());
            
            arrStartIndex.get(i).add( arrStartInd.get(0).get(i));
            arrCountBar.get(i).add( arrCountBr.get(0).get(i));
        }
        for(int i=0; i < this.arrLines.size()-1 ;i++){
                findNeighbour(arrStartInd.get(i), arrCountBr.get(i), arrStartInd.get(i+1), arrCountBr.get(i+1), i+1);
        }
        
        /**Если в найденном комбинации линий меньше, чем в шаблоне, то удаляем комбинацию*/
        int i=0;
        while( i < arrStartIndex.size() ){
            if( arrStartIndex.get(i).size() != arrLines.size() ){
                arrStartIndex.remove(i);
                arrCountBar.remove(i);
                i--;
            }
            i++;
        }   
        /**Если 1 линия возрастастающая, а 2ая - снижающаяся, то максимум снижающейся линии не должен превышать максимум возрастающей и.т.д.*/
            i=0;
            while(i < arrStartIndex.size()){
                for(int j=0; j < arrStartIndex.get(i).size()-1; j++){
                    if( arrLines.get(j).isBuyLine() && arrLines.get(j+1).isSellLine()
                            && arrBars.get( (int)arrStartIndex.get(i).get(j)+(int)arrCountBar.get(i).get(j)-1 ).getPriceH() < arrBars.get( (int)arrStartIndex.get(i).get(j+1) ).getPriceH()
                            ||
                            arrLines.get(j).isSellLine() && arrLines.get(j+1).isBuyLine()
                            && arrBars.get( (int)arrStartIndex.get(i).get(j)+(int)arrCountBar.get(i).get(j)-1 ).getPriceL() > arrBars.get( (int)arrStartIndex.get(i).get(j+1) ).getPriceL()){
                        arrStartIndex.remove(i);
                        arrCountBar.remove(i);
                        i--;
                        break;
                    }  
                }
                i++;
            }
    }
    private void createArrCombinationLinesOnChart(){
        for(int i=arrComboLines.size(); i < this.arrStartIndex.size();i++){
            arrComboLines.add(new ArrayList<LineOnChart>());
            for(int j=0; j < this.arrStartIndex.get(i).size(); j++){
                arrComboLines.get(i).add( new LineOnChart( arrLines.get(j).isBuyLine(), arrLines.get(j).isSellLine(),
                                                                        (int)this.arrStartIndex.get(i).get(j), (int)this.arrCountBar.get(i).get(j),
                                                                        returnMinPriceInLine( (int)this.arrStartIndex.get(i).get(j), (int)this.arrCountBar.get(i).get(j) ),
                                                                        returnMaxPriceInLine((int)this.arrStartIndex.get(i).get(j), (int)this.arrCountBar.get(i).get(j)) ));
            }
        }
        /**Временный код*/
        //Если есть равные комбинации, то вывести об этом сообщение
        boolean b=false;
        int num;
        label:    for(int i =0;i<arrComboLines.size(); i++)
                for(int j=0; j< arrComboLines.size();j++){
                    if(i!=j && arrComboLines.get(i).size() == arrComboLines.get(j).size()){
                        num=0;
                        for(int k=0;k< arrComboLines.get(i).size();k++)
                            if(arrComboLines.get(i).get(k) == arrComboLines.get(j).get(k) ){
                                num++;
                            }
                        if(num == arrComboLines.get(i).size()){
                            System.out.println("ATTENTION!!! ЕСТЬ полное совпадение комбинации");
                            break label;
                        }
                    }
                }
        System.out.println("Полных совпадений комбинаций нет. Это ХОРОШО");
        /***/
    }
    public void findTemplate(){
        this.arrLines = arrLines;
        
        ArrayList<ArrayList> arrStartBarIndex = new ArrayList<ArrayList>();
        ArrayList<ArrayList> arrCountBarInLine = new ArrayList<ArrayList>();
        for(int j=0; j < arrLines.size(); j++){
            arrStartBarIndex.add(new ArrayList());
            arrCountBarInLine.add(new ArrayList());
        }
     /** Нахождение каждой линии*/       
        for(int i=0; i < arrBars.size(); i++){
            for(int j=0; j < arrLines.size(); j++){
//                if( arrLines.get(j).getMaxCountBar() != 0 )
                for( int p = arrLines.get(j).getMinCountBar() ;
                        p <  arrLines.get(j).getMaxCountBar()+1 && i+p <= arrBars.size(); 
                        p++){
                    if( findLine( i, p, arrLines.get(j) ) ){
                        arrStartBarIndex.get(j).add(i);
                        arrCountBarInLine.get(j).add(p);
                    }                       
                }
            }
        }   
        collectRelevantLines(arrStartBarIndex, arrCountBarInLine); 
        
        createArrCombinationLinesOnChart();
    }
    
    
    void printArr(ArrayList<ArrayList> arr){
        for(int i=0; i < arr.size(); i++){
            System.out.println();
            for(int j=0; j< arr.get(i).size();j++) 
                System.out.println(""+i+"."+j+") "+arr.get(i).get(j));
        }    
    }
    void printArr(ArrayList<ArrayList> arr1, ArrayList<ArrayList> arr2){
        for(int i=0; i < arr1.size(); i++){
            System.out.println();
            for(int j=0; j< arr1.get(i).size();j++) 
                System.out.println(""+i+"."+j+")\t"+arr1.get(i).get(j)+"\t"+arr2.get(i).get(j));
        }    
    }
    void printCombo(){
        for(int i=0;i<this.arrComboLines.size();i++){
            System.out.println();
            for(int j=0;j<this.arrComboLines.get(i).size();j++){
                this.arrComboLines.get(i).get(j).print();
            }
        }
    }

}