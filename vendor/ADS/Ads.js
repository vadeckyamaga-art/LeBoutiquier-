const fakeAds = [
    { 
        text: "Boostez votre business dès aujourd'hui !", 
        image: "../Image/BurnaBoy.jpeg" 
    },
    { 
        text: "Investissez malin — Découvrez nos offres", 
        image: "../Image/ville.jpg" 
    },
    { 
        text: "Formation en ligne — 50% de réduction !", 
        image: "../Image/BEYAH.jpeg" 
    },
    { 
        text: "Offre Spéciale — Cliquez ici !", 
        image: "../Image/parfum.jpeg" 
    }
];

let currentIndex = 0;
const banner = document.getElementById('ad-banner');
const adImage = document.getElementById('ad-image');
const adText = document.getElementById('ad-text');

function updateAd() {
    banner.style.opacity='0';
    setTimeout(() => {
        const ad=fakeAds[currentIndex];
        adImage.src=ad.image;
        adText.textContent=ad.text;
        currentIndex=(currentIndex+1)%fakeAds.length;
        banner.style.opacity='1';
    }, 500);
}

updateAd();
setInterval(updateAd, 3000);