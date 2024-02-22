wp.blocks.registerBlockType("fictional-university/blog-index", {
  title: "Fictional University Blog Index",
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
    "Blog Index Placeholder"
  );
}
