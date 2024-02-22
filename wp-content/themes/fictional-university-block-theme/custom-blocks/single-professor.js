wp.blocks.registerBlockType("fictional-university/single-professor", {
  title: "Fictional University Single Professor",
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
    "Single Professor Placeholder"
  );
}
