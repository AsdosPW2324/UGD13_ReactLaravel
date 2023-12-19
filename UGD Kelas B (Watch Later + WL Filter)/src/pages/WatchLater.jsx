// @ts-nocheck
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
  Form,
} from "react-bootstrap";
import { GetWatchLater, DeleteFromWatchLater } from "../api/apiWatchLater";
import { getThumbnail } from "../api";

const WatchLater = () => {
  const [isLoading, setIsLoading] = useState(false);
  const [contents, setContents] = useState([]);
  const [isPending, setIsPending] = useState(false);
  const [showConfirmationModal, setShowConfirmationModal] = useState(false);
  const [contentIdToDelete, setContentIdToDelete] = useState(null);
  const [filterDate, setFilterDate] = useState("")

  const deleteFromFavorites = (id) => {
    setContentIdToDelete(id);
    setShowConfirmationModal(true);
  };

  const handleDeleteConfirmed = () => {
    setIsPending(true);

    DeleteFromWatchLater(contentIdToDelete)
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
    GetWatchLater({
      filter: filterDate
    })
      .then((response) => {
        setContents(response);
        console.log(response);
        setIsLoading(false);
      })
      .catch((err) => {
        console.log(err);
        setIsLoading(false);
      });
  };

  //make a function to substring the content.dateadded to get the date
  const substringDate = (date) => {
    return date?.substring(0, 10);
  };


  useEffect(() => {
    fetchContents();
  }, [filterDate]);

  return (
    <Container className="mt-4">
      <Stack direction="horizontal" gap={3} className="mb-3">
        <h1 className="h4 fw-bold mb-0 text-nowrap">Watch Later Videos</h1>
        <hr className="border-top border-light opacity-50 w-100" />
        <Form.Group controlId="formFilter">
          <Form.Select
            size="sm"
            style={{ width: "200px" }}
            value={filterDate}
            onChange={(e) => setFilterDate(e.target.value)}
          >
            <option value="" disabled hidden>
              Filter Watch Later
            </option>
            <option value="all">Semua video watch later</option>
            <option value="today">Hari ini</option>
            <option value="yesterday">Kemarin</option>
            <option value="this-month">Bulan ini</option>
            <option value="this-year">Tahun ini</option>
          </Form.Select>
        </Form.Group>
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
        <div>
          {contents?.map((content) => (
            <div key={content.id}>
              <div className="card text-white flex-row w-100 overflow-auto mb-3">
                <img
                  src={getThumbnail(content.thumbnail)}
                  className="object-fit-cover bg-light"
                  style={{ aspectRatio: "16 / 9", height: "200px" }}
                  alt="..."
                />
                <div className="card-body">
                  <Stack direction="horizontal">
                    <h4 className="card-title text-truncate">
                      {content.title}
                    </h4>
                    <p className="card-text ms-auto">
                      Tanggal Ditambahkan: {substringDate(content.DateAdded)}
                    </p>
                  </Stack>
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
                      Apakah kamu yakin ingin menghapus ini dari watch later?
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
            </div>
          ))}
        </div>
      ) : (
        <Alert variant="dark" className="text-center">
          Belum ada video di Watch later, yuk tambah Video !
        </Alert>
      )}
    </Container>
  );
};

export default WatchLater;
