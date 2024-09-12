<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="module">
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/10.13.1/firebase-app.js";
        import {
            getDatabase,
            ref,
            set,
            get,
            update,
            remove
        } from "https://www.gstatic.com/firebasejs/10.13.1/firebase-database.js";

        const firebaseConfig = {
            apiKey: "AIzaSyDb6l5NL5KoTikbUwF-Bzxhl3i_Ig9h2Gk",
            authDomain: "cookbook-35a9e.firebaseapp.com",
            databaseURL: "https://cookbook-35a9e-default-rtdb.firebaseio.com",
            projectId: "cookbook-35a9e",
            storageBucket: "cookbook-35a9e.appspot.com",
            messagingSenderId: "598777255184",
            appId: "1:598777255184:web:56624e2f7b5a621b544195"
        };

        const app = initializeApp(firebaseConfig);
        const db = getDatabase(app);

        document.addEventListener("DOMContentLoaded", function() {
            const addCategoryForm = document.getElementById("add-category-form");
            const editCategoryForm = document.getElementById("edit-category-form");
            const categoryList = document.getElementById("category-list");

            if (!addCategoryForm || !editCategoryForm || !categoryList) {
                console.error("Các phần tử không tồn tại.");
                return;
            }

            function fetchCategories() {
                get(ref(db, 'category')).then((snapshot) => {
                    if (snapshot.exists()) {
                        const categories = snapshot.val();
                        categoryList.innerHTML = "";
                        categoryList.innerHTML = `<tr>
                            <td>Tên danh mục</td>
                            <td>Hình ảnh danh mục</td>
                            <td>Thao tác</td>
                        </tr>`;
                        for (const [id, category] of Object.entries(categories)) {
                            categoryList.innerHTML += `
                            <tr>
                                <td>${category.name}</td>
                                <td><img src="${category.image}" alt="Category Image" width="50"></td>
                                <td><button onclick="editCategory('${id}', '${category.name}', '${category.image}')" class="btn btn-warning btn-sm">Sửa</button>
                                    <button onclick="deleteCategory('${id}')" class="btn btn-danger btn-sm">Xóa</button>
                                </td>
                            </tr>
                            `;
                        }
                    } else {
                        categoryList.innerHTML = "Không có danh mục nào.";
                    }
                }).catch((error) => {
                    console.error("Có lỗi khi lấy dữ liệu: ", error);
                });
            }

            // Add category
            addCategoryForm.addEventListener("submit", (e) => {
                e.preventDefault();
                const name = addCategoryForm.name.value;
                const image = addCategoryForm.image.value;
                const newCategoryRef = ref(db, 'category/' + Date.now());
                set(newCategoryRef, {
                    name: name,
                    image: image
                }).then(() => {
                    addCategoryForm.reset();
                    fetchCategories();
                }).catch((error) => {
                    console.error("Có lỗi khi thêm danh mục sản phẩm : ", error);
                });
            });

            window.editCategory = function(id, name, image) {
                editCategoryForm.id.value = id;
                editCategoryForm.name.value = name;
                editCategoryForm.image.value = image;
                document.getElementById("edit-category-form").style.display = "block";
            };

            editCategoryForm.addEventListener("submit", (e) => {
                e.preventDefault();
                const id = editCategoryForm.id.value;
                const name = editCategoryForm.name.value;
                const image = editCategoryForm.image.value;
                const categoryRef = ref(db, 'category/' + id);
                update(categoryRef, {
                    name: name,
                    image: image
                }).then(() => {
                    editCategoryForm.reset();
                    document.getElementById("edit-category-form").style.display = "none";
                    fetchCategories();
                }).catch((error) => {
                    console.error("Lỗi khi cập nhật dữ liệu: ", error);
                });
            });

            window.deleteCategory = function(id) {
                const categoryRef = ref(db, 'category/' + id);
                remove(categoryRef).then(() => {
                    fetchCategories();
                }).catch((error) => {
                    console.error("Có lỗi khi xóa danh mục: ", error);
                });
            };

            fetchCategories();
        });
    </script>
</head>

<body>
    <h2 class="text-center mt-2 mb-4">Quản lý danh mục món ăn</h2>

    <form id="add-category-form" class="m-auto" style="border:1px solid #004972; padding: 12px;border-radius: 5px;">
        <h5 class="text-center text-success">Thêm danh mục</h5>
        <label for="name">Tên danh mục món ăn <span style="color: red;">*</span></label>
        <input type="text" name="name" placeholder="Tên danh mục" class="form-control mb-2" required>
        <label for="name">Đường dẫn hình ảnh danh mục <span style="color: red;">*</span></label>
        <input type="text" name="image" placeholder="URL hình ảnh" class="form-control mb-2" required>
        <button type="submit" class="btn btn-success text-center m-auto">Thêm</button>
    </form>

    <form id="edit-category-form" class="m-auto mt-5" style="border:1px solid #004972; padding: 12px;border-radius: 5px;display: none;">
        <h5 class="text-center text-warning mt-4">Sửa danh mục</h5>
        <input type="hidden" id="id">
        <label for="name">Tên danh mục món ăn <span style="color: red;">*</span></label>
        <input type="text" id="name" placeholder="Tên danh mục" class="form-control mb-2" required>
        <label for="name">Đường dẫn hình ảnh danh mục <span style="color: red;">*</span></label>
        <input type="text" id="image" placeholder="URL hình ảnh" class="form-control mb-2" required>
        <button type="submit" class="btn btn-warning">Cập nhật</button>
    </form>


    <h4 class="text-center mt-4">Danh sách danh mục</h4>
    <table id="category-list" class="table table-hover table-bordered text-center">
        <tr>
            <td>Tên danh mục</td>
            <td>Hình ảnh</td>
            <td>Thao tác</td>
        </tr>

    </table>

</body>

</html>

<style>
    #add-category-form,
    #edit-category-form {
        width: 40%;
    }

    @media (max-width: 768px) {

        #add-category-form,
        #edit-category-form {
            width: 90%;
        }
    }
</style>