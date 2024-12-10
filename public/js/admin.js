let timeoutDuration = 10 * 60 * 1000; // 10 minutes
let warningDuration = 1 * 60 * 1000; // 1 minute before logout

let warningTimeout = setTimeout(() => {
    alert("Your session will expire soon due to inactivity.");
}, timeoutDuration - warningDuration);

let logoutTimeout = setTimeout(() => {
    window.location.href = "/logout"; // Adjust your logout route
}, timeoutDuration);

document.addEventListener("mousemove", resetTimeout);
document.addEventListener("keydown", resetTimeout);

function resetTimeout() {
    clearTimeout(warningTimeout);
    clearTimeout(logoutTimeout);

    warningTimeout = setTimeout(() => {
        alert("Your session will expire soon due to inactivity.");
    }, timeoutDuration - warningDuration);

    logoutTimeout = setTimeout(() => {
        window.location.href = "/logout";
    }, timeoutDuration);
}

function updateAssets() {
    fetch('/update-assets', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    }).then((response) => response.json()).then((data)=>{
        document.getElementById('update').innerText = 'Bid rate: '+parseFloat(data.bid)+'; Ask bit: '+parseFloat(data.ask);
    });

}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addTradeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let form = e.target;
        const formData = new FormData(form);

        const formObject = {};
        formData.forEach((value, key) => {
            formObject[key] = value;
        });

        fetch('/user/open/trade', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formObject)
        }).then((response) => response.json()).then((data)=>{
            if(data.reload == 1){
                window.location.reload();
            }else{
                var container = document.getElementById('button-trade');
                //var conn = document.createElement('div');

                container.innerHTML = data.error;
                container.className = 'error';
                container.style.margin='15px 120px';
                container.style.textAlign='center';
                container.style.padding='10px';
                container.style.color='#fff';
                container.style.background='red';
                container.style.borderRadius = '8px';
                //container.insertBefore(conn, container);
            }
        });
    });
});