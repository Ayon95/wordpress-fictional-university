import $ from 'jquery';
import DOMPurify from 'dompurify';

class MyNotes {
  constructor() {
    this.addEventListeners();
  }

  addEventListeners() {
    $('.create-note form').on('submit', this.createNote.bind(this));
    // the event handler will be executed if the click event is triggered by the Delete button inside #my-notes
    $('#my-notes').on('click', '.delete-note', this.deleteNote.bind(this));
    $('#my-notes').on('click', '.edit-note', this.toggleEditMode.bind(this));
    $('#my-notes').on('click', '.update-note', this.updateNote.bind(this));
  }

  // This will make the title and body of a note editable, and will show a Save button to save the changes
  toggleEditMode(e) {
    const clickedButton = $(e.target.closest('button'));
    const noteElement = clickedButton.parent(['data-note-id']);

    if (clickedButton.attr('data-editable') === 'true') {
      this.makeNoteReadonly(noteElement);
      this.restoreNoteData(noteElement);
    } else {
      this.makeNoteEditable(noteElement);
    }
  }

  makeNoteEditable(noteElement) {
    const editButton = noteElement.find('.edit-note');
    // Turn the edit button into a cancel button (when clicked, it will disable edit mode)
    editButton.attr('data-editable', 'true');
    editButton.html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');

    noteElement
      .find('.note-title-field, .note-body-field')
      .removeAttr('readonly')
      .addClass('note-active-field');

    noteElement.find('.update-note').addClass('update-note--visible');

    // Save current note data so that it can be restored when the user cancels edit mode
    noteElement.data('currentData', {
      title: noteElement.find('.note-title-field').val(),
      content: noteElement.find('.note-body-field').val(),
    });
  }

  makeNoteReadonly(noteElement) {
    const editButton = noteElement.find('.edit-note');

    // Turn the cancel button into an edit button (when clicked, it will enable edit mode)
    editButton.attr('data-editable', 'false');
    editButton.html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit');

    noteElement
      .find('.note-title-field, .note-body-field')
      .attr('readonly', '')
      .removeClass('note-active-field');

    noteElement.find('.update-note').removeClass('update-note--visible');
  }

  restoreNoteData(noteElement) {
    noteElement.find('.note-title-field').val(noteElement.data('currentData').title);
    noteElement.find('.note-body-field').val(noteElement.data('currentData').content);
  }

  insertNoteIntoNoteList(note) {
    const noteHtml = `
    <li data-note-id="${DOMPurify.sanitize(note.id)}">
      <input readonly class="note-title-field" type="text" value="${DOMPurify.sanitize(
        note.title,
      )}">
      <button class="edit-note" data-editable="false">
        <i class="fa fa-pencil" aria-hidden="true"></i>
        Edit
      </button>
      <button class="delete-note">
        <i class="fa fa-trash-o" aria-hidden="true"></i>
        Delete
      </button>
      <textarea readonly class="note-body-field">${DOMPurify.sanitize(note.body)}</textarea>
      <button class="update-note btn btn--blue btn--small">
        <i class="fa fa-arrow-right" aria-hidden="true"></i>
        Save
      </button>
    </li>
    `;

    $('#my-notes').prepend(noteHtml).hide().slideDown();
  }

  // Submit handler for create note form
  async createNote(e) {
    e.preventDefault();

    const form = $(e.target);
    const titleField = form.find('.new-note-title');
    const bodyField = form.find('.new-note-body');

    const newNoteData = {
      title: titleField.val(),
      content: bodyField.val(),
      status: 'private',
    };

    try {
      const savedNote = await $.ajax({
        url: `${dataFromWp.siteUrl}/wp-json/wp/v2/note`,
        method: 'POST',
        dataType: 'json',
        headers: { 'X-WP-Nonce': dataFromWp.nonce },
        data: newNoteData,
      });

      // Show the newly-created note on the frontend
      this.insertNoteIntoNoteList({
        id: savedNote.id,
        title: savedNote.title.raw,
        body: savedNote.content.raw,
      });

      // Clear form fields
      titleField.val('');
      bodyField.val('');
    } catch (error) {
      console.error(error);
    }
  }

  // Save updated note data to the database
  async updateNote(e) {
    const noteElement = $(e.target).parent('[data-note-id]');
    const updatedNoteData = {
      title: noteElement.find('.note-title-field').val(),
      content: noteElement.find('.note-body-field').val(),
    };

    try {
      await $.ajax({
        url: `${dataFromWp.siteUrl}/wp-json/wp/v2/note/${noteElement.attr('data-note-id')}`,
        method: 'POST',
        dataType: 'json',
        headers: { 'X-WP-Nonce': dataFromWp.nonce },
        data: updatedNoteData,
      });
      this.makeNoteReadonly(noteElement);
    } catch (error) {
      console.error(error);
    }
  }

  async deleteNote(e) {
    const noteElement = $(e.target).parent('[data-note-id]');

    try {
      await $.ajax({
        url: `${dataFromWp.siteUrl}/wp-json/wp/v2/note/${noteElement.attr('data-note-id')}`,
        method: 'DELETE',
        dataType: 'json',
        headers: { 'X-WP-Nonce': dataFromWp.nonce },
      });
      noteElement.slideUp();
    } catch (error) {
      console.error(error);
    }
  }
}

export default MyNotes;
