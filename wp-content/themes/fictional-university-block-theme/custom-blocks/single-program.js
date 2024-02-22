wp.blocks.registerBlockType("fictional-university/single-program", {
  title: "Fictional University Single Program",
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
    "Single Program Placeholder"
  );
}
