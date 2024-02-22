wp.blocks.registerBlockType("fictional-university/header", {
  title: "Fictional University Header",
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
    "Header Placeholder"
  );
}
