class PopupBuilder { 
    constructor(headerObj, products, upsells, crossells) { 
       this.header = headerObj; 
       this.products = products;
       this.upsells = upsells;
       this.crossells = crossells;
    } 

    constructor () {}

    test() { 
       console.log("The height of the polygon: ", this.h) 
       console.log("The width of the polygon: ",this. w) 
    } 

    static buildHeader(headerObj) {
        this.header = headerObj; 
        return this;
    }

    static buildProductSection(products) {
        this.products = products; 
        return this;
    }

    static buildUpsellSection(upsells) {
        this.upsells = upsells; 
        return this;
    }

    static buildCrossellSection(crossells) {
        this.crossells = crossells; 
        return this;
    }

    static buildFooterSection(footers) {
        this.footers = footers; 
        return this;
    }

    static build() {
        return '<div style="padding-left: 20px; padding-right: 20px;">' + this.header + this.products + this.upsells + this.crossells + this.footers + "</div>";
    }

 } 