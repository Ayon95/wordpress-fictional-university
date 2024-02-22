wp.blocks.registerBlockType("fictional-university/single-post", {
  title: "Fictional University Single Post",
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
    "Single Post Placeholder"
  );
}
