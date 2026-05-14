// 4 domande
const QUESTIONS = [
  {
    id: "q1_phase",
    text: "In che fase di sviluppo del tuo business ti trovi?",
    options: [
      { value: "A", label: "Sto muovendo i primi passi ma il business non è ancora (pienamente) attivo" },
      { value: "B", label: "Ho un’attività già avviata, che sta crescendo molto" },
      { value: "C", label: "Ho un’attività già avviata che sta attraversando un momento di crisi" },
      { value: "D", label: "La mia attività sta cambiando gestione (acquisizione o passaggio generazionale)" }
    ]
  },
  {
    id: "q2_business",
    text: "Di cosa ti occupi?",
    options: [
      { value: "A", label: "B2B - vendita a PMI e professionisti" },
      { value: "B", label: "B2C - vendita a privati" },
      { value: "C", label: "Arte, cultura e artigianato" },
      { value: "D", label: "Non profit e associazioni" }
    ]
  },
  {
    id: "q3_topic",
    text: "Quale argomento ti interessa di più?",
    options: [
      { value: "A", label: "Marketing & Branding" },
      { value: "B", label: "Business Planning & Event Management" },
      { value: "C", label: "Sviluppo & Franchising" },
      { value: "D", label: "Raccolta fondi & Finanziamenti" },
      { value: "E", label: "Skills Imprenditoriali & Sviluppo Team" },
      { value: "F", label: "Pocket Manager & Business Strategy" }
    ]
  },
  {
    id: "q4_multipotential",
    text: "Ultima domanda: se dico “multipotenziale”, tu dici… ?",
    options: [
      { value: "a", label: "Sono io! *-*" },
      { value: "b", label: "Eh? Di cosa si tratta?" },
      { value: "c", label: "Non fa per me" }
    ]
  }
];

const qaEl = document.getElementById("qa");

let step = 0;
const answers = {}; // { q1_phase: "A", ... }

function renderQuestion(animated = false) {
  const q = QUESTIONS[step];

  const markup = `
    <div class="qa-inner ${animated ? "is-entering" : ""}">
      <div class="question">${q.text}</div>
      <div class="answers">
        ${q.options.map(opt => `
          <button class="answer-btn" type="button" data-value="${opt.value}">
            ${opt.label}
          </button>
        `).join("")}
      </div>
    </div>
  `;

  // Se non vogliamo animazione (prima render), mettiamo subito
  if (!animated || qaEl.innerHTML.trim() === "") {
    qaEl.innerHTML = markup;
    bindAnswerClicks(q.id);
    return;
  }

  // Se vogliamo animazione: prima facciamo uscire il contenuto attuale
  const current = qaEl.querySelector(".qa-inner");
  if (current) {
    current.classList.remove("is-entering");
    current.classList.add("is-leaving");

    // quando finisce l'uscita, sostituisci con il nuovo e fai entrare
    current.addEventListener("animationend", () => {
      qaEl.innerHTML = markup;
      bindAnswerClicks(q.id);
    }, { once: true });
  } else {
    // fallback
    qaEl.innerHTML = markup;
    bindAnswerClicks(q.id);
  }
}

function bindAnswerClicks(questionId){
  qaEl.querySelectorAll(".answer-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      answers[questionId] = btn.dataset.value;
      goNext(true); // next con animazione
    });
  });
}

function derivedFields(payloadAnswers) {
  return {
    goToPocketMethod: payloadAnswers.q3_topic === "F",
    showM1: payloadAnswers.q4_multipotential === "a" || payloadAnswers.q4_multipotential === "b"
  };
}

function saveAndGoHome(reason = "completed") {
  QUESTIONS.forEach(q => {
    if (!(q.id in answers)) answers[q.id] = null;
  });

  const payload = {
    version: "1.0",
    reason,
    submittedAt: new Date().toISOString(),
    answers: { ...answers },
    derived: derivedFields(answers)
  };

  const json = JSON.stringify(payload, null, 2);
  localStorage.setItem("pocket_questionnaire", json);

  const redirectTo = payload.derived.goToPocketMethod
  ? "https://pocketmanager.herewestart.it/pocket-method/"
  : "https://pocketmanager.herewestart.it/landinghome/";

  window.parent.postMessage(
    {
      type: "POCKET_QUESTIONNAIRE_DONE",
      redirectTo,
      payload
    },
    "*"
  );
}

function finalizeAndShowJSON(reason = "completed") {
  // se mancano risposte (skip), le mettiamo a null
  QUESTIONS.forEach(q => {
    if (!(q.id in answers)) answers[q.id] = null;
  });

  const payload = {
    version: "1.0",
    reason, // "completed" oppure "skipped"
    submittedAt: new Date().toISOString(),
    answers: { ...answers },
    derived: derivedFields(answers)
  };

  const json = JSON.stringify(payload, null, 2);

  qaEl.innerHTML = `
    <div class="result">
      <h2>JSON finale</h2>
      <pre id="jsonOut"></pre>

      <div class="result-actions">
        <button class="smallbtn" id="downloadBtn" type="button">Download JSON</button>
        <button class="smallbtn" id="restartBtn" type="button">Ricomincia</button>
      </div>
      <div class="hint">Se vuoi, nel prossimo step useremo questo JSON per decidere la landing.</div>
    </div>
  `;

  qaEl.querySelector("#jsonOut").textContent = json;

  qaEl.querySelector("#downloadBtn").addEventListener("click", () => {
    const blob = new Blob([json], { type: "application/json;charset=utf-8" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "risposte-questionario.json";
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  });
  localStorage.setItem("pocket_questionnaire", json);
  qaEl.querySelector("#restartBtn").addEventListener("click", () => {
    step = 0;
    for (const k of Object.keys(answers)) delete answers[k];
    renderQuestion();
  });
}

function goNext(animated = false) {
  step++;

  // Se abbiamo finito le domande, prima "cloudOut" e poi mostriamo il JSON
  if (step >= QUESTIONS.length) {
    if (animated) {
      const current = qaEl.querySelector(".qa-inner");
      if (current) {
        current.classList.remove("is-entering");
        current.classList.add("is-leaving");
        current.addEventListener("animationend", () => {
          saveAndGoHome("completed");
        }, { once: true });
        return; // important: aspettiamo l’animazione
      }
    }

    // fallback (se non c’è qa-inner o animated=false)
    saveAndGoHome("completed");
    return;
  }

  // altrimenti vai alla prossima domanda (con o senza animazione)
  renderQuestion(animated);
}

// Note: skip button removed from markup; skip behavior intentionally omitted.

// start
renderQuestion(false);