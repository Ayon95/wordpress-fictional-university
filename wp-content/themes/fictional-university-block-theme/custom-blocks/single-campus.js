wp.blocks.registerBlockType("fictional-university/single-campus", {
  title: "Fictional University Single Campus",
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
    "Single Campus Placeholder"
  );
}
