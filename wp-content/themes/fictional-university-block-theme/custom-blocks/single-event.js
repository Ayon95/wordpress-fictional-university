wp.blocks.registerBlockType("fictional-university/single-event", {
  title: "Fictional University Single Event",
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
    "Single Event Placeholder"
  );
}
