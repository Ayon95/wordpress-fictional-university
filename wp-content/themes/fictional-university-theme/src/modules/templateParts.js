export function professorListHTML(professors) {
  return `
  <ul class="professor-cards">
    ${professors
      .map(
        professor => `
        <li class="professor-card__list-item">
          <a class="professor-card" href="${professor.permalink}">
            <img class="professor-card__image" src="${professor.image_url}" alt="${professor.caption}">
            <span class="professor-card__name">${professor.title}</span>
          </a>
        </li>
      `,
      )
      .join('')}
  </ul>
`;
}

export function eventCardHTML(event) {
  return `
    <article class="event-summary">
    <a class="event-summary__date t-center" href="${event.permalink}">
      <span class="event-summary__month">${event.month}</span>
      <span class="event-summary__day">${event.day}</span>
    </a>
    <div class="event-summary__content">
      <h5 class="event-summary__title headline headline--tiny">
        <a href="${event.permalink}">${event.title}</a>
      </h5>
      ${event.excerpt ? `<p>${event.excerpt} <a href="${event.permalink}">Learn more</a></p>` : ''}
    </div>
  </article>
  `;
}
