import { FaTrash } from "react-icons/fa";
import { toast } from "react-toastify";
import { useEffect, useState } from "react";
import {
  Alert,
  Col,
  Container,
  Row,
  Spinner,
  Stack,
  Button,
  Modal,
} from "react-bootstrap";
import { GetAllFavorite, DeleteFromFavorites } from "../api/apiFavorite";
import { getThumbnail } from "../api";

const ContentPage = () => {
  const [isLoading, setIsLoading] = useState(false);
  const [contents, setContents] = useState([]);
  const [isPending, setIsPending] = useState(false);
  const [showConfirmationModal, setShowConfirmationModal] = useState(false);
  const [contentIdToDelete, setContentIdToDelete] = useState(null);

  const deleteFromFavorites = (id) => {
    setContentIdToDelete(id);
    setShowConfirmationModal(true);
  };

  const handleDeleteConfirmed = () => {
    setIsPending(true);

    DeleteFromFavorites(contentIdToDelete)
      .then((response) => {
        setIsPending(false);
        toast.success(response.message);
        fetchContents();
      })
      .catch((err) => {
        console.log(err);
        setIsPending(false);
        toast.dark(err.message);
      });

    setShowConfirmationModal(false);
  };

  const handleDeleteCanceled = () => {
    setShowConfirmationModal(false);
  };

  const fetchContents = () => {
    setIsLoading(true);
    GetAllFavorite()
      .then((response) => {
        setContents(response);
        setIsLoading(false);
      })
      .catch((err) => {
        console.log(err);
        setIsLoading(false);
      });
  };

  useEffect(() => {
    fetchContents();
  }, []);

  return (
    <Container className="mt-4">
      <Stack direction="horizontal" gap={3} className="mb-3">
        <h1 className="h4 fw-bold mb-0 text-nowrap">My Favorite Videos</h1>
        <hr className="border-top border-light opacity-50 w-100" />
      </Stack>

      {isLoading ? (
        <div className="text-center">
          <Spinner
            as="span"
            animation="border"
            variant="primary"
            size="lg"
            role="status"
            aria-hidden="true"
          />
          <h6 className="mt-2 mb-0">Loading...</h6>
        </div>
      ) : contents?.length > 0 ? (
        <Row>
          {contents?.map((content) => (
            <Col md={6} lg={4} className="mb-3" key={content.id}>
              <div
                className="card text-white"
                style={{ aspectRatio: "16 / 9" }}
              >
                <img
                  src={getThumbnail(content.thumbnail)}
                  className="card-img w-100 h-100 object-fit-cover bg-light"
                  alt="..."
                />
                <div className="card-body">
                  <h5 className="card-title text-truncate">{content.title}</h5>
                  <p className="card-text">{content.description}</p>
                  <Button
                    variant="danger"
                    onClick={() => deleteFromFavorites(content.id)}
                    className="position-absolute bottom-0 end-0 mb-3 me-3"
                  >
                    <FaTrash className="mx-1 mb-1" />
                  </Button>
                </div>
              </div>
              {showConfirmationModal && (
                <Modal
                  show={showConfirmationModal}
                  onHide={handleDeleteCanceled}
                >
                  <Modal.Body>
                    <p className="mx-3 mt-3">
                      Apakah kamu yakin ingin menghapus video ini dari favorit?
                    </p>
                  </Modal.Body>
                  <Modal.Footer>
                    <Button variant="secondary" onClick={handleDeleteCanceled}>
                      Batal
                    </Button>
                    <Button variant="danger" onClick={handleDeleteConfirmed}>
                      Hapus
                    </Button>
                  </Modal.Footer>
                </Modal>
              )}
            </Col>
          ))}
        </Row>
      ) : (
        <Alert variant="dark" className="text-center">
          Belum ada video, yuk tambah video favorit !
        </Alert>
      )}
    </Container>
  );
};

export default ContentPage;
