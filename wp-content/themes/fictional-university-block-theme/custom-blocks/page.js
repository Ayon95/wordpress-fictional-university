wp.blocks.registerBlockType("fictional-university/page", {
  title: "Fictional University Page",
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
    "Page Content Placeholder"
  );
}
