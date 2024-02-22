import $ from 'jquery';

class ProfessorLike {
  constructor() {
    this.addEventListeners();
  }

  addEventListeners() {
    $('.like-box').on('click', this.handleClickLike.bind(this));
  }

  // Toggle like - if the user has already liked the professor, remove the like
  // Otherwise, add a like
  handleClickLike(e) {
    const likeBox = $(e.target).closest('.like-box');
    const userHasLiked = likeBox.attr('data-exists') === 'yes';

    if (userHasLiked) {
      this.deleteLike(likeBox);
    } else {
      this.createLike(likeBox);
    }
  }

  async createLike(likeBox) {
    try {
      const professorId = likeBox.attr('data-professor-id');
      const response = await $.ajax({
        url: `${dataFromWp.siteUrl}/wp-json/university/v1/like`,
        method: 'POST',
        dataType: 'json',
        data: { professor_id: professorId },
        headers: { 'X-WP-Nonce': dataFromWp.nonce },
      });

      // Update UI

      likeBox.attr('data-like-id', response.id);
      likeBox.attr('data-exists', 'yes');

      const likeCountElement = likeBox.find('.like-count');
      const likeCount = Number.parseFloat(likeCountElement.text());

      likeCountElement.text(likeCount + 1);
    } catch (error) {
      console.error(error);
    }
  }

  async deleteLike(likeBox) {
    try {
      const likeId = likeBox.attr('data-like-id');
      const response = await $.ajax({
        url: `${dataFromWp.siteUrl}/wp-json/university/v1/like/${likeId}`,
        method: 'DELETE',
        dataType: 'json',
        headers: { 'X-WP-Nonce': dataFromWp.nonce },
      });

      console.log(response);

      // Update UI

      likeBox.attr('data-like-id', '');
      likeBox.attr('data-exists', 'no');

      const likeCountElement = likeBox.find('.like-count');
      const likeCount = Number.parseFloat(likeCountElement.text());

      likeCountElement.text(likeCount - 1);
    } catch (error) {
      console.error(error);
    }
  }
}

export default ProfessorLike;
