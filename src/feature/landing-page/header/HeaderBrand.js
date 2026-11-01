export default class HeaderBrand {
    constructor(brandImg, brandImgAlt, brandName, brandNameSPan){ 
        this.brandContainer = document.createElement('a');
        this.brandContainer.className = "nav-brand"
        this.brandContainer.href = "/";
        
        this.brandImg = document.createElement('img');
        this.brandImg.className = 'img';
        this.brandImg.alt = brandImgAlt;
        this.brandImg.src = brandImg;

        this.span = document.createElement('span');
        this.span.textContent = brandNameSPan;

        this.brandName = document.createElement('div');
        this.brandName.textContent = brandName;
        this.brandName.className = "nav-name";
        this.brandName.appendChild(this.span);
    }
    getHeaderBrand(){
        this.brandContainer.appendChild(this.brandImg);
        this.brandContainer.appendChild(this.brandName);
        return this.brandContainer;
    }
}