import express from "express";
import "colors";
import * as path from "path";
import * as fs from "fs/promises";
import cookieParser from "cookie-parser";
import {parse} from 'node-html-parser';
import {multipleTemplate, questions, singleTemplate} from "./questions.js";

const PORT = process.env.PORT || 80;

const app = express();
app.use(cookieParser());

let currentPage = 1;
let visitedPages = {};
let endDate;

app.get("/restart", (req, res) => {
    currentPage = 1;
    visitedPages = {};
    endDate = new Date((new Date).getTime() + 30*60000);
    res.redirect("/mod/quiz/attempt.php")
});

app.use("/mod/quiz/attempt.php", express.static(path.resolve("static")));
app.get("/mod/quiz/attempt.php", async (req, res) => {
    const queryPage = req.query.page;
    if (queryPage) currentPage = queryPage;
    currentPage = Math.min(30, currentPage);
    currentPage = Math.max(1, currentPage);

    let file = (await fs.readFile(path.resolve("static", "base.htm"))).toString();
    let html = parse(file);

    renderQuestion(html, currentPage);
    renderNavigation(html, currentPage, visitedPages);
    renderTimer(html, endDate);

    html.querySelector("body").innerHTML += `
    <script>
    (() => {
        const url = new URL(window.location.href);
    url.searchParams.set("page", ${currentPage});
    window.history.pushState(null, null, url.toString());
    })();
    </script>
    `

    visitedPages[currentPage] = true;
    res.setHeader("Content-Type", "text/html");
    res.send(html.toString());
});
app.get("/next", (req, res) => {
    currentPage++;
    res.redirect("/mod/quiz/attempt.php");
});
app.get("/prev", (req, res) => {
    currentPage--;
    console.log("prev")
    res.redirect("/mod/quiz/attempt.php");
});


function renderQuestion(html, currentPage) {
    const model = questions[currentPage];

    const template = model.type === "single" ? singleTemplate : multipleTemplate;

    html.querySelector(".ablock").innerHTML = template.ablock;

    const $textQuestion = html.querySelector(".qtext > p");
    $textQuestion.textContent = model.question;

    const $answer = html.querySelector(".answer");
    for (let variant of model.variants) {
        const $variant = parse(template.variant);
        console.log($variant)
        // const $label = $variant.querySelector("label");
        // const id = Math.random();
        // $label.setAttribute("for", id);
        // $variant.setAttribute("id", id);
        $variant.querySelector(".variant-text").textContent = variant;
        $answer.innerHTML += $variant;
    }

    html.querySelector(".qno").textContent = currentPage;
}

function renderNavigation(html, currentPage, visitedPages) {
    const $qnButtons = html.querySelector(".qn_buttons");
    const qnButtons = $qnButtons.querySelectorAll(".qnbutton");
    qnButtons[currentPage - 1].classList.add("thispage");
    html.querySelector("head").innerHTML += `
    <script>
    document.addEventListener("click", (e) => {
        const isInsideQnButton = e.target.classList.contains("qnbutton") || e.target.closest(".qnbutton"); 
        if(!isInsideQnButton) return;
        const $qnbutton = e.target.classList.contains("qnbutton") ? e.target : e.target.closest(".qnbutton");
        e.stopImmediatePropagation();
        e.preventDefault();
        const questionIndex = $qnbutton.dataset.quizPage;
        console.log(e.target.dataset);
        window.open("/mod/quiz/attempt.php?page=" + (+questionIndex + 1), "_self");
    }, true);
    </script>`;
    if(currentPage == 1) {
        html.querySelector("body").innerHTML += `
        <script>       
        document.querySelector(".mod_quiz-prev-nav").hidden = true;
        </script>"
        `
    }
    html.querySelector("head").innerHTML += `
    <script>
    document.addEventListener("click", e => {
       if(!e.target.classList.contains("mod_quiz-prev-nav")) return;
       e.preventDefault();
       e.stopImmediatePropagation();
       window.open("/prev", "_self");
    }, true);
    </script>`
    Object.keys(visitedPages).forEach(index => {
        qnButtons[index - 1].classList.add("answersaved");
        qnButtons[index - 1].classList.remove("notyetanswered");
    });
}

function renderTimer(html, endDate) {
    let $timer = html.querySelector("#quiz-time-left");
    $timer.setAttribute("data-end-date", +endDate)
}

app.listen(PORT, () => console.log(`Server started at ${PORT}`.yellow));