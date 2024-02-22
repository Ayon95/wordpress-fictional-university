wp.blocks.registerBlockType("fictional-university/campus-archive", {
  title: "Fictional University Campus Archive",
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
    "Campus Archive Placeholder"
  );
}
