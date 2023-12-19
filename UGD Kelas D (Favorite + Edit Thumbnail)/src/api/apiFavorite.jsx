import useAxios from ".";

export const GetAllFavorite = async () => {
  try {
    const response = await useAxios.get("/favourites", {
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

export const AddToFavorites = async (id_content) => {
  try {
    const response = await useAxios.post(
      "/favourites",
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

export const DeleteFromFavorites = async (id) => {
  try {
    const response = await useAxios.delete(`/favourites/${id}`, {
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
