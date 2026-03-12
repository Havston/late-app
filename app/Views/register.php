<div class="camera-wrapper">

<div class="card camera-card">

<h2>Регистрация опоздания</h2>

<video id="video" autoplay muted playsinline></video>

<div class="camera-controls">
<button id="startBtn" class="btn btn-primary">Начать запись</button>
<button id="stopBtn" class="btn btn-danger" disabled>Остановить</button>
</div>

<p id="status" class="camera-status">
Готов к записи
</p>

</div>


<div id="confirmBox" class="card confirm-card" style="display:none">

<p><strong>Проверьте данные</strong></p>

<input type="text" id="nameInput" placeholder="Имя и фамилия" maxlength="100">

<div class="confirm-buttons">
<button id="confirmBtn" class="btn btn-primary">Подтвердить</button>
<button id="cancelBtn" class="btn btn-reset">Отмена</button>
</div>

</div>

</div>


<script>

const startBtn = document.getElementById('startBtn');
const stopBtn = document.getElementById('stopBtn');
const confirmBtn = document.getElementById('confirmBtn');
const cancelBtn = document.getElementById('cancelBtn');

const video = document.getElementById('video');
const nameInput = document.getElementById('nameInput');
const confirmBox = document.getElementById('confirmBox');
const status = document.getElementById('status');

let videoStream = null;
let recognition = null;
let recognizedText = "";


/* =========================
   CSRF token
========================= */

const csrfToken = "<?= $_SESSION['csrf'] ?>";


/* =========================
   Вспомогательные функции
========================= */

function capitalize(word){
    return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
}

function extractName(text){

    text = text.toLowerCase();

    const match = text.match(/зовут\s+([а-яё]+\s+[а-яё]+)/);

    if(match){
        const parts = match[1].split(" ");
        return capitalize(parts[0]) + " " + capitalize(parts[1]);
    }

    const words = text.split(" ").filter(w => /^[а-яё]+$/.test(w));

    if(words.length >= 2){
        return capitalize(words[0]) + " " + capitalize(words[1]);
    }

    return "";
}


/* =========================
   Speech Recognition
========================= */

const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

if(SpeechRecognition){

    recognition = new SpeechRecognition();
    recognition.lang = "ru-RU";
    recognition.continuous = false;
    recognition.interimResults = false;

    recognition.onresult = (event) => {

        recognizedText = event.results[0][0].transcript;

        console.log("Распознано:", recognizedText);

    };

    recognition.onerror = (event) => {
        console.error("Ошибка распознавания:", event.error);
    };

}else{

    status.textContent = "Ваш браузер не поддерживает распознавание речи";

}



/* =========================
   Запуск камеры
========================= */

startBtn.onclick = async () => {

    recognizedText = "";
    confirmBox.style.display = "none";

    try{

        videoStream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true
        });

        video.srcObject = videoStream;

    }catch(err){

        alert("Нет доступа к камере");
        console.error(err);
        return;

    }

    if(recognition){
        recognition.start();
    }

    status.textContent = "Идёт запись...";

    startBtn.disabled = true;
    stopBtn.disabled = false;

};



/* =========================
   Остановка записи
========================= */

stopBtn.onclick = () => {

    if(recognition){
        recognition.stop();
    }

    if(videoStream){

        videoStream.getTracks().forEach(track => track.stop());
        video.srcObject = null;

    }

    const extractedName = extractName(recognizedText);

    nameInput.value = extractedName;

    confirmBox.style.display = "block";

    status.textContent = "Проверьте данные";

    startBtn.disabled = false;
    stopBtn.disabled = true;

};



/* =========================
   Подтверждение
========================= */

confirmBtn.onclick = async () => {

    const finalName = nameInput.value.trim();

    if(!finalName){
        alert("Введите имя");
        return;
    }

    if(finalName.length > 100){
        alert("Имя слишком длинное");
        return;
    }

    confirmBtn.disabled = true;

    try{

        const response = await fetch("/late/auto", {

            method: "POST",

            headers:{
                "Content-Type": "application/json",
                "X-CSRF-Token": csrfToken
            },

            body: JSON.stringify({
                student_name: finalName,
                text: recognizedText
            })

        });

        if(!response.ok){
            throw new Error("Ошибка сервера");
        }

        confirmBox.style.display = "none";

        nameInput.value = "";

        status.textContent = "Запись сохранена";

    }catch(err){

        console.error(err);
        alert("Ошибка сохранения");

    }

    setTimeout(()=>{

        status.textContent = "Готов к записи";
        confirmBtn.disabled = false;

    },2000);

};



/* =========================
   Отмена
========================= */

cancelBtn.onclick = () => {

    confirmBox.style.display = "none";
    nameInput.value = "";

    status.textContent = "Отменено";

};

</script>