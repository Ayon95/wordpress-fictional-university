wp.blocks.registerBlockType("fictional-university/footer", {
  title: "Fictional University Footer",
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
    "Footer Placeholder"
  );
}
