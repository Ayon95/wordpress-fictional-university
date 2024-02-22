wp.blocks.registerBlockType("fictional-university/past-events-page", {
  title: "Fictional University Past Events Page",
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
    "Past Events Page Placeholder"
  );
}
