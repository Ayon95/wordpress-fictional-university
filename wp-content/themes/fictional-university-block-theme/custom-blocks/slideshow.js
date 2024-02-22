import { registerBlockType } from "@wordpress/blocks";
import { InnerBlocks } from "@wordpress/block-editor";

registerBlockType("fictional-university/slideshow", {
  title: "Slideshow",
  // If this block is added in the post editor, it will occupy full width of the screen
  supports: {
    align: ["full"],
  },
  attributes: {
    align: { type: "string", default: "full" },
  },
  save: SaveComponent,
  edit: EditComponent,
});

function EditComponent() {
  const containerStyle = {
    backgroundColor: "#333",
    padding: "35px",
  };

  const labelStyle = {
    textAlign: "center",
    fontSize: "20px",
    color: "#FFF",
  };

  return (
    <div style={containerStyle}>
      <p style={labelStyle}>Slideshow</p>
      <InnerBlocks allowedBlocks={["fictional-university/slide"]} />
    </div>
  );
}

function SaveComponent() {
  return <InnerBlocks.Content />;
}
