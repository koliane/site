package financemarket;

/**
 * @author Tyshkovets Nick
 * @version 1.0
 */
public class LineOnChart {

    private boolean buyLine;
    private boolean sellLine;
    private int startBar;
    private int countBar;
    private float minPrice, maxPrice;

    LineOnChart(boolean buyLine, boolean sellLine, int startBar, int countBar, float minPrice, float maxPrice) {
        this.buyLine = buyLine;
        this.sellLine = sellLine;
        this.startBar = startBar;
        this.countBar = countBar;
        this.minPrice = minPrice;
        this.maxPrice = maxPrice;
    }

    LineOnChart(boolean buyLine, boolean sellLine, int startBar, int countBar) {
        this.buyLine = buyLine;
        this.sellLine = sellLine;
        this.startBar = startBar;
        this.countBar = countBar;
    }

    LineOnChart(int startBar, int countBar) {
        this.startBar = startBar;
        this.countBar = countBar;
    }

    public int getStartBar() {
        return this.startBar;
    }

    public int getCountBar() {
        return this.countBar;
    }

    public float getMinPrice() {
        return this.minPrice;
    }

    public float getMaxPrice() {
        return this.maxPrice;
    }

    public boolean isBuyLine() {
        return buyLine;
    }

    public boolean isSellLine() {
        return sellLine;
    }

    public void print() {
        System.out.println("startBar = " + startBar + "; countBar = " + countBar + "; minPrice = " + minPrice + "; maxPrice = " + maxPrice);
    }
}
