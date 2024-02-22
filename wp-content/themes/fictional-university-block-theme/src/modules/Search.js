import $ from 'jquery';
import DOMPurify from 'dompurify';
import { professorListHTML, eventCardHTML } from './templateParts';

class Search {
  // Describe and create an object
  constructor() {
    this.insertSearchHTML();
    this.openButton = $('.js-search-trigger');
    this.closeButton = $('.search-overlay__close');
    this.searchOverlay = $('.search-overlay');
    this.searchInput = $('.search-term');
    this.searchResultsContainer = $('.search-overlay__results');
    this.searchOverlayIsOpen = false;
    this.spinnerIsVisible = false;
    this.previousSearchTerm = '';

    this.addEventListeners();
  }

  // Events

  addEventListeners() {
    $(document).on('keydown', this.handleKeypress.bind(this));
    this.openButton.on('click', this.openOverlay.bind(this));
    this.closeButton.on('click', this.closeOverlay.bind(this));

    const getSearchResultsDebounced = this.debounce(this.getSearchResults.bind(this), 400);

    this.searchInput.on('keyup', getSearchResultsDebounced);
  }

  // Methods

  handleKeypress(e) {
    if (e.key !== 's' && e.key !== 'Escape') return;

    if (this.searchOverlayIsOpen && e.key === 'Escape') {
      this.closeOverlay();
      return;
    }

    const anyFormInputFocused = $('input, textarea').is(':focus');

    // Don't want to accidentally open the overlay if a user is typing into an input field or textarea
    if (!this.searchOverlayIsOpen && e.key === 's' && !anyFormInputFocused) {
      this.openOverlay();
    }
  }

  openOverlay() {
    this.searchOverlayIsOpen = true;
    this.searchOverlay.addClass('search-overlay--active');
    $('body').addClass('body-no-scroll');

    // Clear search field and previous search results if any
    this.searchInput.val('');
    this.searchResultsContainer.html('');

    // The search overlay takes 0.3s to appear on the screen
    setTimeout(() => {
      this.searchInput.trigger('focus');
    }, 301);
  }

  closeOverlay() {
    this.searchOverlayIsOpen = false;
    this.searchOverlay.removeClass('search-overlay--active');
    $('body').removeClass('body-no-scroll');
  }

  async getSearchResults() {
    const searchTerm = this.searchInput.val();

    // No need to fetch search results if search input field is empty
    if (!searchTerm) {
      this.searchResultsContainer.html('');
      this.spinnerIsVisible = false;
      return;
    }

    // No need to fetch search results again if search term hasn't changed
    if (searchTerm.trim() === this.previousSearchTerm) return;

    this.previousSearchTerm = searchTerm.trim();

    try {
      this.showLoadingSpinner();

      const searchResults = await $.ajax({
        url: `${dataFromWp.siteUrl}/wp-json/university/v1/search?term=${searchTerm}`,
        dataType: 'json',
      });

      this.searchResultsContainer.html(`
        <div class="row">
          <div class="one-third">
            <h2 class="search-overlay__section-title">General Information</h2>
            ${
              searchResults.general_info.length > 0
                ? this.searchResultsListHTML(searchResults.general_info)
                : `<p>No general information matches that search.</p>`
            }
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">Programs</h2>
            ${
              searchResults.program.length > 0
                ? this.searchResultsListHTML(searchResults.program)
                : `<p>No programs match that search. <a href="${dataFromWp.siteUrl}/programs">View all programs</a>.</p>`
            }
            <h2 class="search-overlay__section-title">Professors</h2>
            ${
              searchResults.professor.length > 0
                ? professorListHTML(searchResults.professor)
                : `<p>No professors match that search.</p>`
            }
          </div>
          <div class="one-third">
            <h2 class="search-overlay__section-title">Campuses</h2>
            ${
              searchResults.campus.length > 0
                ? this.searchResultsListHTML(searchResults.campus)
                : `<p>No campuses match that search. <a href="${dataFromWp.siteUrl}/campuses">View all campuses</a>.</p>`
            }
            <h2 class="search-overlay__section-title">Events</h2>
            ${
              searchResults.event.length > 0
                ? searchResults.event.map(eventCardHTML).join('')
                : `<p>No events match that search. <a href="${dataFromWp.siteUrl}/events">View all events</a>.</p>`
            }
          </div>
        </div>
      `);
    } catch (error) {
      console.error(error);
    } finally {
      this.removeLoadingSpinner();
    }
  }

  searchResultsListHTML(results) {
    return `
    <ul class="link-list min-list">
      ${results
        .map(
          result => `
          <li>
            <a href="${DOMPurify.sanitize(result.permalink)}">${result.title}</a>
            ${result.post_type === 'post' ? `by ${result.author}` : ''}
          </li>
        `,
        )
        .join('')}
    </ul>
  `;
  }

  debounce(callback, delay) {
    let timerId;

    return function (...args) {
      clearTimeout(timerId);

      timerId = setTimeout(function () {
        callback(...args);
      }, delay);
    };
  }

  showLoadingSpinner() {
    if (this.spinnerIsVisible) return;
    this.searchResultsContainer.html('<div class="spinner-loader"></div>');
    this.spinnerIsVisible = true;
  }

  removeLoadingSpinner() {
    if (!this.spinnerIsVisible) return;
    this.searchResultsContainer.children('.spinner-loader').remove('.spinner-loader');
    this.spinnerIsVisible = false;
  }

  insertSearchHTML() {
    $('body').append(`
    <div class="search-overlay">
      <div class="search-overlay__top">
        <div class="container">
          <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
          <input type="text" class="search-term" id="search-term" placeholder="What are you looking for?">
          <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
        </div>
      </div>
      <div class="container">
        <div class="search-overlay__results"></div>
      </div>
    </div>
    `);
  }
}

export default Search;
