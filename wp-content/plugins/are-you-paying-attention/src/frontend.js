import { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";
import "./frontend.scss";

const containers = document.querySelectorAll(".aypa-frontend-root");

containers.forEach((container) => {
  const quizData = JSON.parse(atob(container.dataset.quiz));
  ReactDOM.createRoot(container).render(<Quiz data={quizData} />);
  // This will be helpful in case we want to track if the block has been hydrated with React
  container.setAttribute("data-hydrated", "true");
});

function Quiz({ data }) {
  const [correctAnswerSelected, setCorrectAnswerSelected] = useState(null);
  const blockStyles = {
    backgroundColor: data.bgColor,
    textAlign: data.textAlignment,
  };

  function handleClickAnswer(answer) {
    if (answer === data.correctAnswer) {
      setCorrectAnswerSelected(true);
    } else {
      setCorrectAnswerSelected(false);
    }
  }
  return (
    <div className="aypa-frontend" style={blockStyles}>
      <p className="aypa-frontend__question">{data.question}</p>
      <ul className="aypa-frontend__answers">
        {data.answers.map((answer) => {
          const isCorrect = answer === data.correctAnswer;
          return (
            <li
              key={answer}
              className={`aypa-frontend__answer aypa-frontend__answer--${
                isCorrect ? "correct" : "incorrect"
              } ${correctAnswerSelected && !isCorrect ? "faded" : ""}`}
            >
              <button
                onClick={() => handleClickAnswer(answer)}
                disabled={correctAnswerSelected}
              >
                {correctAnswerSelected && isCorrect && (
                  <svg
                    role="img"
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    className="bi bi-check"
                    viewBox="0 0 16 16"
                  >
                    <title>Correct answer</title>
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z" />
                  </svg>
                )}
                {correctAnswerSelected && !isCorrect && (
                  <svg
                    role="img"
                    xmlns="http://www.w3.org/2000/svg"
                    width="20"
                    height="20"
                    className="bi bi-x"
                    viewBox="0 0 16 16"
                  >
                    <title>Incorrect answer</title>
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                  </svg>
                )}
                {answer}
              </button>
            </li>
          );
        })}
      </ul>
      <div
        className={`message message--correct ${
          correctAnswerSelected ? "message--visible" : ""
        }`}
      >
        <svg
          role="img"
          aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          className="bi bi-emoji-smile"
          viewBox="0 0 16 16"
        >
          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
          <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.5 3.5 0 0 0 8 11.5a3.5 3.5 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5" />
        </svg>
        <p>That is correct!</p>
      </div>
      <div
        className={`message message--incorrect ${
          correctAnswerSelected === false ? "message--visible" : ""
        }`}
        onAnimationEnd={() => setCorrectAnswerSelected(null)}
      >
        <svg
          role="img"
          aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          className="bi bi-emoji-frown"
          viewBox="0 0 16 16"
        >
          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
          <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.5 3.5 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.5 4.5 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5" />
        </svg>
        <p>Sorry, try again.</p>
      </div>
    </div>
  );
}
