import useAxios from ".";

export const GetWatchLater = async (filterQuery) => {
  try {
    const response = await useAxios.get(`/watch_laters?${new URLSearchParams(filterQuery).toString()}`, {
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${sessionStorage.getItem("token")}`,
      },
    });
    return response.data.data;
  } catch (error) {
    throw error.response.data;
  }
};

export const AddToWatchLater = async (id_content) => {
  try {
    const response = await useAxios.post(
      "/watch_laters",
      { id_content },
      {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${sessionStorage.getItem("token")}`,
        },
      }
    );
    return response.data;
  } catch (error) {
    throw error.response.data;
  }
};

export const DeleteFromWatchLater = async (id) => {
  try {
    const response = await useAxios.delete(`/watch_laters/${id}`, {
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${sessionStorage.getItem("token")}`,
      },
    });
    return response.data;
  } catch (error) {
    throw error.response.data;
  }
};
