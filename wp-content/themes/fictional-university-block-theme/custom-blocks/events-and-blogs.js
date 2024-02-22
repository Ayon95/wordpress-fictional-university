wp.blocks.registerBlockType("fictional-university/events-and-blogs", {
  title: "Events and Blogs",
  save: SaveComponent,
  edit: EditComponent,
});

function SaveComponent() {
  return null;
}

function EditComponent() {
  return wp.element.createElement(
    "div",
    { className: "placeholder-block" },
    "Events and Blogs Placeholder"
  );
}
