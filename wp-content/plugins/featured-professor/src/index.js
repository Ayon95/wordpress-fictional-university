import "./index.scss";
import { useState, useEffect } from "react";
import apiFetch from "@wordpress/api-fetch";
import { registerBlockType } from "@wordpress/blocks";
import { useBlockProps } from "@wordpress/block-editor";
import { store as coreDataStore } from "@wordpress/core-data";
import { useSelect } from "@wordpress/data";
import { Spinner } from "@wordpress/components";
import metadata from "./block.json";

registerBlockType(metadata, {
  edit: EditComponent,
  save: SaveComponent,
});

function EditComponent({ attributes, setAttributes }) {
  // A Professor's related programs are not included in the result obtained from getEntityRecords() since 'related_programs' is an ACF field
  // This is why we have to make an HTTP request to a custom endpoint to get the necessary data
  const [selectedProfessor, setSelectedProfessor] = useState(null);

  const blockProps = useBlockProps({
    className: "featured-professor-wrapper",
  });

  const { isLoading, professors } = useSelect((select) => {
    const queryOptions = { per_page: -1 };
    const selectorArgs = ["postType", "professor", queryOptions];
    return {
      professors: select(coreDataStore).getEntityRecords(...selectorArgs),
      isLoading: !select(coreDataStore).hasFinishedResolution(
        "getEntityRecords",
        selectorArgs
      ),
    };
  }, []);

  useEffect(() => {
    if (!attributes.professorId) return;

    (async function () {
      try {
        const response = await apiFetch({
          path: `/fp-plugin/v1/professor/${attributes.professorId}`,
        });
        setSelectedProfessor(response);
      } catch (error) {
        console.error(error);
      }
    })();

    updateFeaturedProfessorMeta();
  }, [attributes.professorId]);

  // Cleanup when the block is removed
  // Update featured_professor meta so that featured_professor meta rows associated with the removed block are deleted from the database
  useEffect(() => {
    return () => updateFeaturedProfessorMeta();
  }, []);

  function updateFeaturedProfessorMeta() {
    const featuredProfessorBlocks = wp.data
      .select("core/block-editor")
      .getBlocks()
      .filter((block) => block.name === metadata.name);

    const professorIds = new Set(
      featuredProfessorBlocks.map((block) => block.attributes.professorId)
    );

    wp.data
      .dispatch("core/editor")
      .editPost({ meta: { featured_professor: [...professorIds] } });
  }

  return (
    <div {...blockProps}>
      {isLoading ? (
        <Spinner />
      ) : !professors?.length ? (
        <p>No professors to show.</p>
      ) : (
        <>
          <div className="professor-select-container">
            <select
              value={attributes.professorId}
              onChange={(e) => setAttributes({ professorId: e.target.value })}
            >
              <option value="">Select a professor</option>
              {professors.map((professor) => (
                <option key={professor.id} value={professor.id}>
                  {professor.title.rendered}
                </option>
              ))}
            </select>
          </div>
          {selectedProfessor && (
            <div className="featured-professor">
              <img
                src={selectedProfessor.image.url}
                alt={selectedProfessor.image.caption}
                className="featured-professor__photo"
              />
              <div className="featured-professor__text">
                <h3>{selectedProfessor.title}</h3>
                <p>
                  {selectedProfessor.content.replace("&hellip;", "")}&hellip;
                </p>
                {selectedProfessor.related_programs.length > 0 && (
                  <p>
                    Programs taught:{" "}
                    {selectedProfessor.related_programs.join(", ")}
                  </p>
                )}
                <a href={selectedProfessor.url}>
                  Learn more about {selectedProfessor.title} &raquo;
                </a>
              </div>
            </div>
          )}
        </>
      )}
    </div>
  );
}

function SaveComponent() {
  return null;
}
